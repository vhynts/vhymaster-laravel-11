<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'email',
        'password',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function generateUserId()
    {
        $latestUser = User::orderBy('code', 'desc')->first();
        if (!$latestUser) {
            // No users yet, starting with USR-0001
            return 'USR-0001';
        }

        $latestUserCode = $latestUser->code;
        $number = (int) substr($latestUserCode, 4); // Extract the numeric part
        $newNumber = $number + 1;
        $newUserCode = 'USR-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        // Check if the new code already exists
        while (User::where('code', $newUserCode)->exists()) {
            $newNumber++;
            $newUserCode = 'USR-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        }

        return $newUserCode;
    }

}
