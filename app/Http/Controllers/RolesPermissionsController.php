<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Yajra\DataTables\DataTables;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Http\Request;

class RolesPermissionsController extends Controller
{
    public function rolesIndex()
    {
        $judul = 'Peran';

        return view('zRolesPermissions.peranIndex', compact('judul'));
    }

    public function rolesDataAjax(Request $request)
    {
        $peran = DB::table('roles')->select('*');

        return DataTables::of($peran)
            ->addIndexColumn()  // This will automatically add DT_RowIndex
            ->make(true);
    }
    public function permissionsDataAjax(Request $request)
    {
        $peran = DB::table('permissions')->select('*');

        return DataTables::of($peran)
            ->addIndexColumn()  // This will automatically add DT_RowIndex
            ->make(true);
    }

    public function rolesStore(Request $request)
    {
        $request->validate([
            'namaPeran' => 'required|string|unique:roles,name',
        ], [
            'namaPeran.required' => 'Nama peran wajib diisi.',
            'namaPeran.unique' => 'Nama peran sudah ada.',
        ]);

        $role = new Role();
        $role->name = $request->input('namaPeran');
        $role->save();

        return response()->json(['success' => 'Role added successfully.']);
    }

    public function permissionsStore(Request $request)
    {
        $request->validate([
            'namaIzin' => 'required|string|unique:roles,name',
        ], [
            'namaIzin.required' => 'Nama izin wajib diisi.',
            'namaIzin.unique' => 'Nama izin sudah ada.',
        ]);

        $permisson = new Permission();
        $permisson->name = $request->input('namaIzin');
        $permisson->save();

        return response()->json(['success' => 'Permission added successfully.']);
    }


    public function rolesUpdate(Request $request, $id)
    {
        $request->validate([
            'namaPeranEdit' => 'required|string|unique:roles,name,' . $id,

        ], [
            'namaPeranEdit.required' => 'Nama peran wajib diisi.',
            'namaPeranEdit.unique' => 'Nama peran sudah ada.',

        ]);

        $role = Role::findOrFail($id);
        $role->name = $request->input('namaPeranEdit');

        $role->save();

        return redirect()->route('rolesIndex')->with('success', 'Role updated successfully.');
    }
    public function permissionsUpdate(Request $request, $id)
    {
        $request->validate([
            'namaIzinEdit' => 'required|string|unique:roles,name,' . $id,

        ], [
            'namaIzinEdit.required' => 'Nama Izin wajib diisi.',
            'namaIzinEdit.unique' => 'Nama Izin sudah ada.',

        ]);

        $permission = Permission::findOrFail($id);
        $permission->name = $request->input('namaIzinEdit');

        $permission->save();

        return redirect()->route('permissionsIndex')->with('success', 'Role updated successfully.');
    }


    public function rolesHapus($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->route('rolesIndex')->with('success', 'Peran berhasil dihapus.');
    }

    public function permissionsHapus($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return redirect()->route('permissionsIndex')->with('success', 'Izin berhasil dihapus.');
    }


    public function permissionsIndex()
    {
        $judul = 'Izin';

        return view('zRolesPermissions.izinIndex', compact('judul'));
    }


    public function kelolaRolesIndex()
    {
        $judul = 'Kelola Peran';

        $roles = Role::all();
        $permissions = Permission::all();


        return view('zRolesPermissions.kelolaPeranIndex', compact('judul', 'roles', 'permissions'));
    }

    public function kelolaRolesUpdate(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::find($request->role_id);

        // Sync permissions - this will assign and remove permissions as needed
        $role->syncPermissions($request->permissions);

        return redirect()->route('kelolaRolesIndex')->with('success', '<strong>Permissions</strong> updated successfully.');
    }

    public function getPermissionsByRoles($roleId)
    {
        $role = Role::findOrFail($roleId);
        // 
        // $permissions = Permission::all();
        $permissions = Permission::orderBy('name')->get();

        $permissionsWithStatus = $permissions->map(function ($permission) use ($role) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'assigned' => $role->hasPermissionTo($permission->name),
            ];
        });



        return response()->json(['permissions' => $permissionsWithStatus]);
    }


}
