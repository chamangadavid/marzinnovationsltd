<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define permissions
        $permissions = [
            'manage transactions',
            'manage news management',
            'manage promotions',
            'manage services',
            'manage gallery',
            'manage contact',
            'manage receipts',
            'manage access control',
        ];

        // Create and store all permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create a super admin role and assign all permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminRole->syncPermissions(Permission::all());

        // Create administrator user
        $admin = User::firstOrCreate(
            ['email' => 'Administrator@gmail.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('12345678'),
            ]
        );

        // Assign Super Admin role to user
        $admin->assignRole($superAdminRole);
    }
}
