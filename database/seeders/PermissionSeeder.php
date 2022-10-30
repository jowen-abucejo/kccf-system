<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $arrayOfRoleNames = [
            'admin',
            'registrar',
            'cashier',
            'instructor',
            'student',
            'applicant',
        ];

        $arrayOfPermissionNames = [
            'create registration',
            'read registration',
            'update registration',
            'delete registration',
            'create student',
            'read student',
            'update student',
            'delete student',
            'create users',
            'read users',
            'update users',
            'delete users',
            'create program',
            'read program',
            'update program',
            'delete program',
            'create subject',
            'read subject',
            'update subject',
            'delete subject',
            'create payment',
            'read payment',
            'update payment',
            'delete payment',
            'create grade',
            'read grade',
            'update grade',
            'delete grade',
            'create school settings',
            'read school settings',
            'update school settings',
            'delete school settings',
        ];

        $roles = collect($arrayOfRoleNames)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'api'];
        });

        $permissions = collect($arrayOfPermissionNames)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'api'];
        });

        Role::insert($roles->toArray());

        // Permission::insert($permissions->toArray());
    }
}
