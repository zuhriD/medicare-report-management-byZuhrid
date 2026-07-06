<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $adminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        
        $developerRole = Role::firstOrCreate(['name' => 'developer', 'guard_name' => 'web']);
        $leadRole = Role::firstOrCreate(['name' => 'lead', 'guard_name' => 'web']);
        
        $developerPermissions = [
            'view_daily::report',
            'view_any_daily::report',
            'create_daily::report',
            'view_module::platform',
            'view_any_module::platform',
            'update_module::platform',
        ];

        $developerRole->syncPermissions($developerPermissions);

        // Sync users based on their existing role column
        $users = User::all();
        foreach ($users as $user) {
            if ($user->role === 'admin') {
                $user->assignRole($adminRole);
            } elseif ($user->role === 'developer') {
                $user->assignRole($developerRole);
            } elseif ($user->role === 'lead') {
                $user->assignRole($leadRole);
            }
        }
    }
}
