<?php

namespace Modules\UserManagement\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Roles
        $adminRole = Role::create(['name' => 'Super Admin']);

        // Create Permissions
        Permission::create(['name' => 'add user']);
        Permission::create(['name' => 'edit user']);
        Permission::create(['name' => 'delete user']);
        Permission::create(['name' => 'view user']);

        // Assign Permissions to Roles
        $adminRole->givePermissionTo(['add user', 'edit user', 'delete user', 'view user']);


        // Create Admin User
        $adminUser = User::create([
            'name' => 'Praveen suthar',
            'email' => 'thesothua@gmail.com',
            'password' => Hash::make('password123'),
        ]);
        
        $adminUser->assignRole($adminRole);
    }
}
