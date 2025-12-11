<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        // $addWorker = Permission::create(['name' => 'add worker']);
        // $editWorker = Permission::create(['name' => 'edit worker']);
        // $deleteWorker = Permission::create(['name' => 'delete worker']);

        // Create roles
        // $admin = Role::create(['name' => 'Admin']);
        // $factoryManager = Role::create(['name' => 'Factory Manager']);

        // Assign permissions to roles
        // $admin->permissions()->attach([$addWorker->id, $editWorker->id, $deleteWorker->id]);
        // $factoryManager->permissions()->attach([$addWorker->id, $editWorker->id]);
    }
}
