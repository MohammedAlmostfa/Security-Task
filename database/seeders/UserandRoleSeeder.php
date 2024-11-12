<?php

namespace Database\Seeders;

use to;
use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserandRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $role_admin = Role::create([
            'role_name' => 'admin',
            'description' => 'يحق له ما لايحق لغيره',
        ]);
        $role_user = Role::create([
            'role_name' => 'user',
            'description' => 'يحق له بعض الشغلات',
        ]);

        // Create users with hashed passwords
        $person_admin = User::create([
            'name' => 'Mohammed ALmostfa',
            'email' => 'mohammedalmostfa36@gmail.com',
            'password' => Hash::make('123456789'),
            'role_id' => $role_admin->id,
        ]);
        $person_user = User::create([
            'name' => 'Ali ALmostfa',
            'email' => 'alialmostfa36@gmail.com',
            'password' => Hash::make('123456789'),
            'role_id' => $role_user->id,
        ]);

        // Attach permissions to roles if they exist
        $permissions = Permission::all();
        // Attach specific permissions to the user role
        $role_user->permissions()->sync(Permission::whereIn('permission_name', ['task.update.status', 'comment.store','comment.update'])->pluck('id'));


        $role_admin->permissions()->sync($permissions->pluck('id')); // Attach all permissions to admin role
    }
}
