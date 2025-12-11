# Tutorial: Building a Role-Based Authentication System in Laravel

This tutorial will guide you through creating a complete role-based access control (RBAC) authentication system like the one used in the HealthGenie HRM application.

## Prerequisites

- Laravel 11.x installed
- Basic knowledge of Laravel concepts
- Understanding of MVC architecture
- MySQL or similar database

## Table of Contents

1. [Initial Setup](#1-initial-setup)
2. [Install Laravel Breeze](#2-install-laravel-breeze)
3. [Create Role and Permission Models](#3-create-role-and-permission-models)
4. [Database Migrations](#4-database-migrations)
5. [Extend User Model](#5-extend-user-model)
6. [Create Custom Middleware](#6-create-custom-middleware)
7. [Register Middleware](#7-register-middleware)
8. [Create Policies](#8-create-policies)
9. [Seed Database](#9-seed-database)
10. [Route Protection](#10-route-protection)
11. [Frontend Implementation](#11-frontend-implementation)
12. [Testing](#12-testing)

---

## 1. Initial Setup

### Create New Laravel Project

```bash
composer create-project laravel/laravel auth-system
cd auth-system
```

### Configure Database

Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=auth_system
DB_USERNAME=root
DB_PASSWORD=your_password

# Add custom super admin configuration
SUPER_ADMIN_ID=1
AUTH_PASSWORD_TIMEOUT=10800
```

---

## 2. Install Laravel Breeze

Laravel Breeze provides basic authentication scaffolding.

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run build
php artisan migrate
```

This gives you:
- Login/logout functionality
- Password reset
- Email verification
- Basic authentication views

---

## 3. Create Role and Permission Models

### Generate Models

```bash
php artisan make:model Role -m
php artisan make:model Permission -m
```

### Role Model (`app/Models/Role.php`)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Users that belong to this role
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role');
    }

    /**
     * Permissions that belong to this role
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    /**
     * Auto-format role name to Title Case
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Str::title($value);
    }
}
```

### Permission Model (`app/Models/Permission.php`)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Permission extends Model
{
    protected $fillable = ['name'];

    /**
     * Roles that have this permission
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }

    /**
     * Auto-format permission name to Title Case
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Str::title($value);
    }
}
```

---

## 4. Database Migrations

### Create Additional Migrations

```bash
php artisan make:migration create_roles_table
php artisan make:migration create_permissions_table
php artisan make:migration create_user_role_table
php artisan make:migration create_role_permission_table
```

### Roles Migration (`database/migrations/xxxx_create_roles_table.php`)

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
```

### Permissions Migration (`database/migrations/xxxx_create_permissions_table.php`)

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
```

### User-Role Pivot Table (`database/migrations/xxxx_create_user_role_table.php`)

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_role');
    }
};
```

### Role-Permission Pivot Table (`database/migrations/xxxx_create_role_permission_table.php`)

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['role_id', 'permission_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permission');
    }
};
```

### Run Migrations

```bash
php artisan migrate
```

---

## 5. Extend User Model

Update `app/Models/User.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The roles that belong to the user
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    /**
     * Check if user has specific role(s)
     *
     * @param string|array $roleNames
     * @return bool
     */
    public function hasRole($roleNames)
    {
        if (is_string($roleNames)) {
            $roleNames = [$roleNames];
        }

        return $this->roles->contains(function ($role) use ($roleNames) {
            return collect($roleNames)->contains(function ($roleName) use ($role) {
                return Str::title($role->name) === Str::title($roleName);
            });
        });
    }

    /**
     * Check if user has specific permission
     *
     * @param string $permissionName
     * @return bool
     */
    public function hasPermission(string $permissionName): bool
    {
        // Allow all permissions for super admin user
        if ($this->id === (int) env('SUPER_ADMIN_ID', 1)) {
            return true;
        }

        // Allow all permissions for Super Admin role
        if ($this->hasRole('Super Admin')) {
            return true;
        }

        // Check if user has permission through any of their roles
        foreach ($this->roles as $role) {
            if ($role->permissions->contains(function ($permission) use ($permissionName) {
                return Str::title($permission->name) === Str::title($permissionName);
            })) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user is super admin
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->id === (int) env('SUPER_ADMIN_ID', 1) || $this->hasRole('Super Admin');
    }
}
```

---

## 6. Create Custom Middleware

### Generate Middleware

```bash
php artisan make:middleware RoleMiddleware
php artisan make:middleware PermissionMiddleware
```

### Role Middleware (`app/Http/Middleware/RoleMiddleware.php`)

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole($role)) {
            abort(403, 'Unauthorized access. Required role: ' . $role);
        }

        return $next($request);
    }
}
```

### Permission Middleware (`app/Http/Middleware/PermissionMiddleware.php`)

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Authentication required.');
        }

        if (!$permission) {
            abort(403, 'No permission specified.');
        }

        // Support multiple permissions with pipe separator (OR logic)
        $permissions = explode('|', $permission);

        foreach ($permissions as $perm) {
            if ($user->hasPermission(trim($perm))) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized access. Required permission: ' . $permission);
    }
}
```

---

## 7. Register Middleware

Update `bootstrap/app.php`:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register custom middleware aliases
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

---

## 8. Create Policies

### Generate User Policy

```bash
php artisan make:policy UserPolicy --model=User
```

Update `app/Policies/UserPolicy.php`:

```php
<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('Manage Users');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->hasPermission('View Users') || $user->id === $model->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('Create Users');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->hasPermission('Edit Users') || $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->hasPermission('Delete Users') && $user->id !== $model->id;
    }
}
```

### Register Policy

Update `app/Providers/AuthServiceProvider.php`:

```php
<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
```

---

## 9. Seed Database

### Create Seeders

```bash
php artisan make:seeder RoleSeeder
php artisan make:seeder PermissionSeeder
php artisan make:seeder UserSeeder
php artisan make:seeder RolePermissionSeeder
```

### Role Seeder (`database/seeders/RoleSeeder.php`)

```php
<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Super Admin',
            'Admin',
            'HR',
            'HR Executive',
            'Manager',
            'Employee',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}
```

### Permission Seeder (`database/seeders/PermissionSeeder.php`)

```php
<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // User Management
            'Manage Users',
            'View Users',
            'Create Users',
            'Edit Users',
            'Delete Users',

            // Role & Permission Management
            'Manage Roles',
            'Manage Permissions',

            // General Permissions
            'View Dashboard',
            'View Reports',
            'Generate Reports',
            'Manage Settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
```

### User Seeder (`database/seeders/UserSeeder.php`)

```php
<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Assign Super Admin role
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole && !$superAdmin->roles->contains($superAdminRole)) {
            $superAdmin->roles()->attach($superAdminRole);
        }

        // Create HR User
        $hrUser = User::firstOrCreate(
            ['email' => 'hr@example.com'],
            [
                'name' => 'HR Manager',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $hrRole = Role::where('name', 'HR')->first();
        if ($hrRole && !$hrUser->roles->contains($hrRole)) {
            $hrUser->roles()->attach($hrRole);
        }

        // Create Regular Employee
        $employee = User::firstOrCreate(
            ['email' => 'employee@example.com'],
            [
                'name' => 'John Employee',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $employeeRole = Role::where('name', 'Employee')->first();
        if ($employeeRole && !$employee->roles->contains($employeeRole)) {
            $employee->roles()->attach($employeeRole);
        }
    }
}
```

### Role Permission Seeder (`database/seeders/RolePermissionSeeder.php`)

```php
<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin gets all permissions (handled by User model)

        // Admin permissions
        $admin = Role::where('name', 'Admin')->first();
        $adminPermissions = [
            'Manage Users', 'View Users', 'Create Users', 'Edit Users',
            'View Dashboard', 'View Reports', 'Generate Reports'
        ];
        $this->assignPermissions($admin, $adminPermissions);

        // HR permissions
        $hr = Role::where('name', 'HR')->first();
        $hrPermissions = [
            'View Users', 'Create Users', 'Edit Users',
            'View Dashboard', 'View Reports'
        ];
        $this->assignPermissions($hr, $hrPermissions);

        // Employee permissions
        $employee = Role::where('name', 'Employee')->first();
        $employeePermissions = ['View Dashboard'];
        $this->assignPermissions($employee, $employeePermissions);
    }

    private function assignPermissions(Role $role, array $permissionNames): void
    {
        if (!$role) return;

        $permissions = Permission::whereIn('name', $permissionNames)->get();

        foreach ($permissions as $permission) {
            if (!$role->permissions->contains($permission)) {
                $role->permissions()->attach($permission);
            }
        }
    }
}
```

### Update Database Seeder (`database/seeders/DatabaseSeeder.php`)

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,
            RolePermissionSeeder::class,
        ]);
    }
}
```

### Run Seeders

```bash
php artisan db:seed
```

---

## 10. Route Protection

### Create Test Controllers

```bash
php artisan make:controller AdminController
php artisan make:controller UserController
php artisan make:controller DashboardController
```

### Admin Controller (`app/Http/Controllers/AdminController.php`)

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'users' => User::with('roles')->get()
        ]);
    }

    public function users()
    {
        // Policy check is automatic when using authorize
        $this->authorize('viewAny', User::class);

        return view('admin.users', [
            'users' => User::with('roles')->get()
        ]);
    }
}
```

### Dashboard Controller (`app/Http/Controllers/DashboardController.php`)

```php
<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('dashboard', [
            'user' => $user,
            'roles' => $user->roles,
        ]);
    }
}
```

### Update Routes (`routes/web.php`)

```php
<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Protected routes
Route::middleware('auth')->group(function () {
    // Dashboard - all authenticated users
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin routes - role-based protection
    Route::prefix('admin')->name('admin.')->middleware('role:Super Admin|Admin')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');

        // User management - permission-based protection
        Route::get('/users', [AdminController::class, 'users'])
            ->middleware('permission:Manage Users')
            ->name('users');
    });

    // HR routes - multiple roles allowed
    Route::prefix('hr')->name('hr.')->middleware('role:Super Admin|Admin|HR')->group(function () {
        Route::get('/dashboard', function() {
            return view('hr.dashboard');
        })->name('dashboard');

        Route::get('/employees', function() {
            return view('hr.employees');
        })->middleware('permission:View Users')->name('employees');
    });
});

require __DIR__.'/auth.php';
```

---

## 11. Frontend Implementation

### Update Dashboard View (`resources/views/dashboard.blade.php`)

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Welcome, {{ $user->name }}!</h3>

                    <div class="mb-6">
                        <h4 class="font-medium text-gray-700">Your Roles:</h4>
                        <div class="mt-2">
                            @forelse($roles as $role)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                                    {{ $role->name }}
                                </span>
                            @empty
                                <span class="text-gray-500">No roles assigned</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        {{-- Admin Panel --}}
                        @if(auth()->user()->hasRole(['Super Admin', 'Admin']))
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h4 class="font-medium text-blue-900 mb-2">Admin Panel</h4>
                                <p class="text-blue-700 text-sm mb-3">Manage system settings and users</p>
                                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    Access Admin
                                </a>
                            </div>
                        @endif

                        {{-- HR Panel --}}
                        @if(auth()->user()->hasRole(['Super Admin', 'Admin', 'HR']))
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <h4 class="font-medium text-green-900 mb-2">HR Panel</h4>
                                <p class="text-green-700 text-sm mb-3">Manage employees and HR tasks</p>
                                <a href="{{ route('hr.dashboard') }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                    Access HR
                                </a>
                            </div>
                        @endif

                        {{-- User Management --}}
                        @if(auth()->user()->hasPermission('Manage Users'))
                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                                <h4 class="font-medium text-purple-900 mb-2">User Management</h4>
                                <p class="text-purple-700 text-sm mb-3">Create and manage user accounts</p>
                                <a href="{{ route('admin.users') }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                                    Manage Users
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- Permission Check Examples --}}
                    <div class="mt-8 border-t pt-6">
                        <h4 class="font-medium text-gray-700 mb-3">Available Actions:</h4>
                        <div class="space-y-2">
                            @if(auth()->user()->hasPermission('View Reports'))
                                <div class="flex items-center text-green-600">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Can view reports
                                </div>
                            @endif

                            @if(auth()->user()->hasPermission('Generate Reports'))
                                <div class="flex items-center text-green-600">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Can generate reports
                                </div>
                            @endif

                            @if(auth()->user()->isSuperAdmin())
                                <div class="flex items-center text-red-600">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                    Super Admin - All permissions granted
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

### Create Admin Views

Create `resources/views/admin/dashboard.blade.php`:

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">System Overview</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                        <div class="bg-blue-100 border border-blue-300 rounded-lg p-4">
                            <h4 class="font-medium text-blue-900">Total Users</h4>
                            <p class="text-2xl font-bold text-blue-700">{{ $users->count() }}</p>
                        </div>

                        <div class="bg-green-100 border border-green-300 rounded-lg p-4">
                            <h4 class="font-medium text-green-900">Admin Users</h4>
                            <p class="text-2xl font-bold text-green-700">
                                {{ $users->filter(fn($user) => $user->hasRole(['Super Admin', 'Admin']))->count() }}
                            </p>
                        </div>

                        <div class="bg-purple-100 border border-purple-300 rounded-lg p-4">
                            <h4 class="font-medium text-purple-900">HR Users</h4>
                            <p class="text-2xl font-bold text-purple-700">
                                {{ $users->filter(fn($user) => $user->hasRole('HR'))->count() }}
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-lg font-medium">User Management</h4>
                        @can('create', App\Models\User::class)
                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Add New User
                            </button>
                        @endcan
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($users as $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @foreach($user->roles as $role)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mr-1">
                                                    {{ $role->name }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($user->isSuperAdmin())
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Super Admin
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Active
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @can('update', $user)
                                                <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                            @endcan
                                            @can('delete', $user)
                                                <a href="#" class="text-red-600 hover:text-red-900">Delete</a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

### Create HR Dashboard

Create `resources/views/hr/dashboard.blade.php`:

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('HR Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">HR Management Panel</h3>
                    <p>Welcome to the HR dashboard. You have access to employee management features.</p>

                    <div class="mt-6">
                        <h4 class="font-medium mb-2">Available Features:</h4>
                        <ul class="list-disc list-inside space-y-1 text-gray-700">
                            @if(auth()->user()->hasPermission('View Users'))
                                <li>View employee records</li>
                            @endif
                            @if(auth()->user()->hasPermission('Create Users'))
                                <li>Add new employees</li>
                            @endif
                            @if(auth()->user()->hasPermission('Edit Users'))
                                <li>Update employee information</li>
                            @endif
                            @if(auth()->user()->hasPermission('View Reports'))
                                <li>Generate HR reports</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

---

## 12. Testing

### Create Feature Tests

```bash
php artisan make:test AuthenticationTest
php artisan make:test RolePermissionTest
```

### Authentication Test (`tests/Feature/AuthenticationTest.php`)

```php
<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_authenticated_users_can_access_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
    }
}
```

### Role Permission Test (`tests/Feature/RolePermissionTest.php`)

```php
<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_has_all_permissions(): void
    {
        // Create super admin user (ID 1)
        $superAdmin = User::factory()->create(['id' => 1]);

        $this->assertTrue($superAdmin->hasPermission('Any Permission'));
        $this->assertTrue($superAdmin->isSuperAdmin());
    }

    public function test_user_with_super_admin_role_has_all_permissions(): void
    {
        $role = Role::create(['name' => 'Super Admin']);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->assertTrue($user->hasPermission('Any Permission'));
        $this->assertTrue($user->isSuperAdmin());
    }

    public function test_user_can_have_role(): void
    {
        $role = Role::create(['name' => 'Admin']);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->assertTrue($user->hasRole('Admin'));
        $this->assertTrue($user->hasRole(['Admin', 'Manager'])); // Should pass if user has Admin
    }

    public function test_user_can_have_permission_through_role(): void
    {
        $permission = Permission::create(['name' => 'View Users']);
        $role = Role::create(['name' => 'HR']);
        $role->permissions()->attach($permission);

        $user = User::factory()->create();
        $user->roles()->attach($role);

        $this->assertTrue($user->hasPermission('View Users'));
    }

    public function test_admin_middleware_blocks_unauthorized_users(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_admin_middleware_allows_admin_users(): void
    {
        $role = Role::create(['name' => 'Admin']);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(200);
    }

    public function test_permission_middleware_works(): void
    {
        // User without permission
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/users');
        $response->assertStatus(403);

        // User with permission
        $permission = Permission::create(['name' => 'Manage Users']);
        $role = Role::create(['name' => 'Admin']);
        $role->permissions()->attach($permission);
        $user->roles()->attach($role);

        $response = $this->actingAs($user)->get('/admin/users');
        $response->assertStatus(200);
    }
}
```

### Run Tests

```bash
php artisan test
```

---

## Usage Examples

### In Controllers

```php
class ExampleController extends Controller
{
    public function index()
    {
        // Check permission
        if (!auth()->user()->hasPermission('View Data')) {
            abort(403);
        }

        // Or use authorization
        $this->authorize('viewAny', Model::class);

        return view('example.index');
    }

    public function store(Request $request)
    {
        // Check role
        if (!auth()->user()->hasRole(['Admin', 'Manager'])) {
            return redirect()->back()->with('error', 'Insufficient permissions');
        }

        // Process store logic
    }
}
```

### In Blade Templates

```blade
{{-- Check roles --}}
@if(auth()->user()->hasRole('Admin'))
    <button>Admin Only Button</button>
@endif

{{-- Check permissions --}}
@if(auth()->user()->hasPermission('Delete Users'))
    <form method="POST" action="{{ route('users.destroy', $user) }}">
        @csrf @method('DELETE')
        <button type="submit">Delete User</button>
    </form>
@endif

{{-- Check multiple roles --}}
@if(auth()->user()->hasRole(['Admin', 'HR', 'Manager']))
    <div>Management Panel</div>
@endif

{{-- Using Laravel's built-in @can directive --}}
@can('update', $user)
    <a href="{{ route('users.edit', $user) }}">Edit</a>
@endcan
```

### In Routes

```php
// Role-based protection
Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
});

// Permission-based protection
Route::middleware(['auth', 'permission:Manage Users'])->group(function () {
    Route::resource('users', UserController::class);
});

// Multiple roles (OR logic)
Route::middleware(['auth', 'role:Admin|HR|Manager'])->group(function () {
    Route::get('/reports', [ReportController::class, 'index']);
});

// Multiple permissions (OR logic)
Route::middleware(['auth', 'permission:View Reports|Generate Reports'])->group(function () {
    Route::get('/analytics', [AnalyticsController::class, 'index']);
});
```

---

## Advanced Features

### 1. Dynamic Permission Assignment

Create a management interface for assigning roles and permissions:

```bash
php artisan make:controller Admin/RoleController --resource
php artisan make:controller Admin/PermissionController --resource
```

### 2. Audit Logging

Track role and permission changes:

```bash
composer require owen-it/laravel-auditing
```

### 3. Cache Permissions

For better performance, cache user permissions:

```php
// In User model
public function hasPermission(string $permissionName): bool
{
    return Cache::remember("user.{$this->id}.permission.{$permissionName}", 3600, function() use ($permissionName) {
        // Permission check logic
    });
}
```

### 4. API Authentication

Extend the system for API usage with Laravel Sanctum:

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

---

## Summary

You now have a complete role-based authentication system that includes:

✅ **Laravel Breeze Integration** - Complete auth scaffolding
✅ **Role-Based Access Control** - Flexible role system
✅ **Permission System** - Granular permission control
✅ **Custom Middleware** - Route-level protection
✅ **Policy Integration** - Model-level authorization
✅ **Super Admin Functionality** - Bypass permissions for super users
✅ **Database Seeding** - Sample data for testing
✅ **Frontend Integration** - Blade templates with auth checks
✅ **Comprehensive Testing** - Feature tests for all functionality

This system provides the same robust authentication and authorization capabilities as your HealthGenie HRM application, with the flexibility to extend and customize as needed.

### Key Features:
- **Case-insensitive** role and permission checking
- **Multiple role support** with OR logic
- **Super admin bypass** functionality
- **Middleware-based route protection**
- **Policy-based model authorization**
- **Clean, maintainable code structure**

The system is production-ready and follows Laravel best practices for security and performance.
