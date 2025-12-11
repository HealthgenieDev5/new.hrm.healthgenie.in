<?php

namespace App\Http\Controllers\AppSettings;

use App\Http\Controllers\Controller;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    // List all users
    public function index()
    {
        $users = User::with('roles')->paginate(10); // Fetch users with their roles
        $data = [
            "users" => $users,
            "pageTitle" => "Users",

        ];
        return view('app-settings.users.index', $data);
    }

    // Show the form to edit roles for a user
    public function edit(User $user)
    {
        $roles = Role::all(); // Fetch all roles
        $data = [
            "user" => $user,
            "pageTitle" => "Edit User",
            "roles" => $roles
        ];

        return view('app-settings.users.edit', $data);
    }

    // Update the roles for a user
    public function update(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'array', // Roles must be an array
            'roles.*' => 'exists:roles,id', // Each role must exist in the roles table
        ]);

        // Sync roles with the user (assign new roles and remove unselected ones)
        $user->roles()->sync($request->roles);

        return redirect()->route('app-settings.users.index')->with('success', 'User roles updated successfully.');
    }
}
