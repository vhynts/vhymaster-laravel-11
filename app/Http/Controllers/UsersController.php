<?php

namespace App\Http\Controllers;
use App\Models\User;
use Spatie\Permission\Models\Role;

use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;


class UsersController extends Controller
{
    public function index()
    {
        $judul = 'Pengguna';

        $jumlahPengguna = User::count();
        $roles = Role::all();

        return view('zUsers.index', compact('judul', 'jumlahPengguna', 'roles'));
    }

    public function usersDataAjax(Request $request)
    {
        if ($request->ajax()) {
            $customSearch = $request->get('customSearch');
            $statusFilter = $request->get('statusFilter'); // Get status filter value
            $rolesFilter = $request->get('rolesFilter'); // Get roles filter value

            $users = User::with('roles') // Eager load roles
                ->select(['id', 'code', 'name', 'email', 'is_active', 'created_at', 'updated_at', 'last_login',]);

            // Custom search filter
            if ($customSearch) {
                $users->where(function ($q) use ($customSearch) {
                    $q->where('id', 'like', "%$customSearch%")
                        ->orWhere('code', 'like', "%$customSearch%")
                        ->orWhere('name', 'like', "%$customSearch%")
                        ->orWhere('email', 'like', "%$customSearch%");
                });
            }

            // Status filter
            if ($statusFilter !== null && $statusFilter !== '') {
                $users->where('is_active', $statusFilter);
            }

            // Roles filter
            if ($rolesFilter !== null && $rolesFilter !== '') {
                $users->whereHas('roles', function ($q) use ($rolesFilter) {
                    $q->where('id', $rolesFilter); // Filter users by role ID
                });
            }


            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('roles', function ($user) {
                    return $user->roles->pluck('name')->implode(', '); // Format roles as comma-separated string
                })
                ->editColumn('last_login', function ($user) {
                    // Format last_login menggunakan Carbon
                    return $user->last_login ? Carbon::parse($user->last_login)->format('Y-m-d, H:i') : '-';
                })
                ->addColumn('status_login', function ($user) {
                    // Cek apakah user sedang online
                    $isOnline = Cache::has('user-is-online-' . $user->id);
                    return $isOnline ? '<span class="badge bg-success-subtle text-success badge-border">Online</span>' : '<span class="badge bg-dark-subtle text-body badge-border">Offline</span>';
                })
                ->make(true);
        }
    }



    public function userStore(Request $request)
    {
        // Define custom error messages for validation
        $messages = [
            'nama.required' => 'Nama harus diisi.',
            'email.required' => 'Email harus diisi.',
            'status.required' => 'Status harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email tidak boleh lebih dari :max karakter.',
            'email.unique' => 'Email sudah digunakan.',
            'peran.required' => 'Role harus diisi.', // Custom message for role
        ];

        // Validate incoming request data
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', // 'unique:users' checks uniqueness in the 'users' table
            'status' => 'required',
            'peran' => 'required|string|exists:roles,name', // Ensure the role exists
        ], $messages);

        // Create a new user in the database
        $user = User::create([
            // 'user_id' => 'USR0009',
            'code' => User::generateUserId(),
            'name' => $validatedData['nama'],
            'email' => $validatedData['email'],
            'password' => Hash::make('password'), // Hash a default password (consider a better approach for real applications)
            'is_active' => $validatedData['status'],
        ]);

        // Assign the selected role to the user
        $user->assignRole($validatedData['peran']);

        // Return a JSON response indicating success and a redirect URL
        return response()->json(['success' => 'User created successfully', 'redirect_url' => url('/users')]);
    }

    public function userDestroy($id)
    {
        $user = User::where('id', $id)->firstOrFail();

        // Revoke all roles from the user
        $user->roles()->detach();

        // Revoke all permissions from the user
        $user->permissions()->detach();

        // Hapus entri cache terkait pengguna
        Cache::forget('user-is-online-' . $user->id);

        // Hapus sesi pengguna jika pengguna yang sedang login dihapus
        // Menggunakan cache untuk menampilkan status session
        $sessionKey = 'user-session-' . $user->id;
        if (Cache::has($sessionKey)) {
            Cache::forget($sessionKey);
        }


        $user->delete(); // Hapus pengguna

        // return redirect()->route('usersIndex')->with('success', 'User deleted successfully');
        return response()->json(['success' => 'User deleted successfully', 'redirect_url' => url('/users')]);

    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id', // Ganti dengan kolom primary key yang sesuai
        ]);
        // $user = User::find($request->user_id);
        $user = User::where('id', $request->id)->first();

        $newPassword = 'password';

        // Update user password
        $user->password = Hash::make($newPassword);
        $user->save();


        return response()->json([
            'success' => true,
            'message' => 'Password berhasil di-reset'
            // 'message' => 'Password berhasil di-reset. Password baru adalah: ' . $newPassword
        ]);
    }

    public function userEdit($id)
    {
        $user = User::where('id', $id)->firstOrFail();
        $roles = $user->roles->pluck('name'); // Get all role names as an array
        $user->role = $roles;
        return response()->json($user);
    }

    public function userUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);
        // Define custom error messages for validation
        $messages = [
            'nama.required' => 'Nama harus diisi.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email tidak boleh lebih dari :max karakter.',
            'peran.required' => 'Role harus diisi.', // Custom message for role
        ];

        // Validate incoming request data
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255', // 'unique:users' checks uniqueness in the 'users' table
            'status' => 'required|boolean',
            'peran' => 'required|string|exists:roles,name', // Ensure the role exists
        ], $messages);

        $user->name = $validatedData['nama'];
        $user->email = $validatedData['email'];
        $user->is_active = $validatedData['status'];
        $user->updated_at = now();

        $user->save();

        $user->syncRoles($validatedData['peran']); // Sync the user's role

        return response()->json(['success' => 'Pengguna berhasil diupdate', 'redirect_url' => url('/users')]);
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru harus terdiri dari minimal 8 karakter.',
            'new_password.confirmed' => 'Password baru dan konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'errors' => [
                    'current_password' => ['Password saat ini tidak sesuai']
                ]
            ], 422);
        }

        if (Hash::check($request->new_password, $user->password)) {
            return response()->json([
                'errors' => [
                    'new_password' => ['Password baru tidak boleh sama dengan password saat ini']
                ]
            ], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        Auth::logout();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah. Silakan login kembali dengan password baru Anda.'
        ]);

    }

}
