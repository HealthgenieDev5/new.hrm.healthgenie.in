<?php

namespace App\Http\Controllers\AppSettings;

use App\Http\Controllers\Controller;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::with('permissions')->paginate(10);
        $pageTitle = 'Roles Management'; // Page title for the index view
        return view('app-settings.roles.index', compact('roles', 'pageTitle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::all();
        $pageTitle = 'Create New Role'; // Page title for the create view
        return view('app-settings.roles.create', compact('permissions', 'pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->permissions()->sync($request->permissions);

        return redirect()->route('app-settings.roles.index')->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all();

        $groupedPermissions = [];
        foreach ($permissions as $perm) {
            $parts = explode(' ', $perm->name);
            $action = $parts[0];
            $module = trim(implode(' ', array_slice($parts, 1)));

            #Normalize the module name by singularizing it.
            $normalizedModule = Str::singular($module);

            $groupedPermissions[$normalizedModule][] = [
                'id'   => $perm->id,
                'name' => $perm->name
            ];
        }
        ksort($groupedPermissions);

        // dd($groupedPermissions);


        $pageTitle = 'Edit Role: ' . $role->name; // Page title for the edit view
        return view('app-settings.roles.edit', compact('role', 'groupedPermissions', 'pageTitle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update(['name' => $request->name]);
        $role->permissions()->sync($request->permissions);

        return redirect()->route('app-settings.roles.index')->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('app-settings.roles.index')->with('success', 'Role deleted successfully.');
    }
}
