<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::create([
            'name' => 'super-admin'
        ]);

        $operatorRole  = Role::create([
            'name' => 'user'
        ]);

        $permissions = [
            ['name' => 'user'],
            ['name' => 'user-list'],
            ['name' => 'user-create'],
            ['name' => 'user-show'],
            ['name' => 'user-edit'],
            ['name' => 'user-delete'],
            ['name' => 'user-status-update'],
            ['name' => 'user-working-status'],

//            ['name' => 'role'],
//            ['name' => 'role-list'],
//            ['name' => 'role-create'],
//            ['name' => 'role-show'],
//            ['name' => 'role-edit'],
//            ['name' => 'role-delete'],
//
//            ['name' => 'permission'],
//            ['name' => 'permission-list'],
//            ['name' => 'permission-create'],
//            ['name' => 'permission-edit'],
//            ['name' => 'permission-delete'],

            ['name' => 'settings'],

            ['name' => 'task-list'],
            ['name' => 'task-create'],
            ['name' => 'task-show'],
            ['name' => 'task-edit'],
            ['name' => 'task-delete'],

        ];


        foreach($permissions as $item) {
            Permission::create($item);
        }

        $role->syncPermissions(Permission::all());

        $user = User::first();

        $user->assignRole($role);

    }
}
