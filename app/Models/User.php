<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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


    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    public function hasRole($roleNames)
    {
        if (is_string($roleNames)) {
            $roleNames = [$roleNames]; // Convert a single string to an array
        }

        return $this->roles->contains(function ($role) use ($roleNames) {
            return collect($roleNames)->contains(function ($roleName) use ($role) {
                return Str::title($role->name) === Str::title($roleName); // Case-insensitive comparison
            });
        });
    }

    public function hasPermission(string $permissionName): bool
    {
        #Allow all permissions for user with id = 1
        if ($this->id === (int) env('SUPER_ADMIN_ID', 1)) {
            return true;
        }

        if ($this->roles->contains(function ($role) {
            return Str::title($role->name) === 'Super Admin';
        })) {
            return true;
        }

        foreach ($this->roles as $role) {
            if ($role->permissions->contains(function ($permission) use ($permissionName) {
                return Str::title($permission->name) === Str::title($permissionName); // Case-insensitive comparison
            })) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the PSP associated with the user.
     */
    public function psp()
    {
        return $this->hasOne(Psp::class);
    }
}
