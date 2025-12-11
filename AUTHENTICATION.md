# Authentication System Documentation

This document provides a comprehensive overview of the authentication and authorization system implemented in the HealthGenie HRM Laravel application.

## Overview

The application implements a robust role-based access control (RBAC) system using Laravel's built-in authentication features enhanced with custom roles and permissions functionality.

## Core Components

### 1. Authentication Foundation

**Laravel Breeze Integration**
- Uses Laravel Breeze for basic authentication scaffolding
- Session-based authentication with web guard
- Standard Laravel authentication features: login, logout, password reset, email verification

**Configuration**: `config/auth.php`
- Default guard: `web` (session-based)
- User provider: Eloquent model (`App\Models\User`)
- Password reset functionality with 60-minute token expiry

### 2. User Model & Relationships

**Model**: `app/Models/User.php`

**Key Features**:
- Standard Laravel User model with custom role/permission methods
- Many-to-many relationship with roles via `user_role` pivot table
- Relationship with PSP (Production Supervisor) model
- Super admin functionality built-in

**Custom Methods**:
- `hasRole($roleNames)`: Check if user has specific role(s) - supports single role or array
- `hasPermission($permissionName)`: Check if user has specific permission
- Case-insensitive role and permission checking using `Str::title()`

**Super Admin Logic**:
- User ID 1 (or `SUPER_ADMIN_ID` env variable) has all permissions
- Users with "Super Admin" role have all permissions
- Bypasses normal permission checks

### 3. Role & Permission Models

**Role Model**: `app/Models/Role.php`
- Simple model with `name` field
- Many-to-many relationship with users via `user_role` table
- Many-to-many relationship with permissions via `role_permission` table
- Auto-formats role names using `Str::title()` mutator

**Permission Model**: `app/Models/Permission.php`
- Simple model with `name` field
- Many-to-many relationship with roles
- Auto-formats permission names using `Str::title()` mutator

**Database Structure**:
- `users` table: Standard Laravel users table
- `roles` table: Role definitions
- `permissions` table: Permission definitions
- `user_role` table: User-role relationships
- `role_permission` table: Role-permission relationships

### 4. Middleware System

**RoleMiddleware**: `app/Http/Middleware/RoleMiddleware.php`
- Checks if authenticated user has specific role
- Usage: `middleware('role:Admin')`
- Returns 403 if user lacks required role

**PermissionMiddleware**: `app/Http/Middleware/PermissionMiddleware.php`
- Checks if authenticated user has specific permission(s)
- Supports multiple permissions with pipe separator: `permission:View Attendance|Manage Workers`
- Usage: `middleware('permission:View Attendance')`
- Returns 403 if user lacks required permission

### 5. Authorization Routes

**Authentication Routes**: `routes/auth.php`

**Available Routes**:
- `GET/POST /login` - User login
- `GET/POST /forgot-password` - Password reset request
- `GET/POST /reset-password/{token}` - Password reset with token
- `GET /verify-email` - Email verification prompt
- `POST /logout` - User logout

**Note**: User registration is disabled (commented out in routes)

### 6. Route Protection Patterns

**Common Middleware Combinations**:
```php
// Basic authentication
middleware(['auth', 'verified'])

// Permission-based access
middleware(['auth', 'verified', 'permission:View Attendance'])

// Role-based access
middleware(['auth', 'permission:Manage roles'])
```

**Examples from Routes**:
- Attendance routes require "View Attendance" permission
- Manual punching requires "Manual Punching" permission
- User management requires "Manage roles" permission
- Retention groups require "Manage Retention Groups" permission

### 7. Predefined Roles in System

Based on code analysis, common roles include:
- **Super Admin**: Full system access
- **HR**: Human resources staff
- **Admin**: Administrative access
- **Hr Executive**: HR executive level
- **PSP**: Production Supervisor/Planner

### 8. Common Permissions

Examples found in the system:
- "View Attendance"
- "Manual Punching"
- "Manage roles"
- "Manage Retention Groups"

### 9. Policy System

**Location**: `app/Policies/`

The system includes Laravel policies for fine-grained authorization, though many appear to be placeholder implementations returning `false` by default (like `WorkerPolicy.php`).

### 10. Security Features

**Super Admin Protection**:
- Environment-based super admin ID configuration
- Automatic permission bypass for super admin users
- Role-based super admin detection

**Session Security**:
- Session regeneration on login
- Session invalidation on logout
- CSRF protection (Laravel default)

**Password Security**:
- Hashed password storage
- Password reset with token expiration
- Configurable password timeout (default: 3 hours)

## Usage Examples

### Checking User Permissions in Controllers
```php
// Check if user has role
if ($user->hasRole('Admin')) {
    // Allow access
}

// Check if user has permission
if ($user->hasPermission('View Attendance')) {
    // Allow access
}

// Check multiple roles
if ($user->hasRole(['Super Admin', 'HR', 'Admin'])) {
    // Allow access
}
```

### Route Protection
```php
// Single permission
Route::get('/attendance', 'AttendanceController@index')
    ->middleware(['auth', 'verified', 'permission:View Attendance']);

// Multiple permissions (OR logic)
Route::get('/reports', 'ReportController@index')
    ->middleware(['auth', 'permission:View Reports|Generate Reports']);
```

### Blade Template Usage
```php
// Check roles in views
@if(auth()->user()->hasRole('Admin'))
    <button>Admin Only Button</button>
@endif

// Check permissions in views
@if(auth()->user()->hasPermission('Manage Workers'))
    <a href="{{ route('workers.create') }}">Add Worker</a>
@endif
```

## Key Considerations

1. **Case Insensitive**: All role and permission checks are case-insensitive
2. **Super Admin Bypass**: Super admin users bypass all permission checks
3. **Registration Disabled**: User registration is disabled by default
4. **Session-based**: Uses session authentication, not token-based
5. **Middleware Order**: Always include 'auth' middleware before role/permission middleware

## Environment Variables

```env
# Super admin user ID (defaults to 1)
SUPER_ADMIN_ID=1

# Authentication configuration
AUTH_GUARD=web
AUTH_PASSWORD_BROKER=users
AUTH_MODEL=App\Models\User
AUTH_PASSWORD_TIMEOUT=10800
```

This authentication system provides a solid foundation for role-based access control in the HRM application, with flexibility for both role and permission-based authorization patterns.