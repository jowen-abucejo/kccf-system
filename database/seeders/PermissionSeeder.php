<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
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
        $roles = [
            ['name' => 'admin', 'guard_name' => 'api'],
            ['name' => 'registrar', 'guard_name' => 'api'],
            ['name' => 'cashier', 'guard_name' => 'api'],
            ['name' => 'instructor', 'guard_name' => 'api'],
            ['name' => 'student', 'guard_name' => 'api'],
            ['name' => 'applicant', 'guard_name' => 'api'],
        ];

        $permissions = [
            ['name' => 'create registration'],
            ['name' => 'read registration'],
            ['name' => 'update registration'],
            ['name' => 'delete registration'],
            ['name' => 'create student'],
            ['name' => 'read student'],
            ['name' => 'update student'],
            ['name' => 'delete student'],
            ['name' => 'create users'],
            ['name' => 'read users'],
            ['name' => 'update users'],
            ['name' => 'delete users'],
            ['name' => 'create program'],
            ['name' => 'read program'],
            ['name' => 'update program'],
            ['name' => 'delete program'],
            ['name' => 'create subject'],
            ['name' => 'read subject'],
            ['name' => 'update subject'],
            ['name' => 'delete subject'],
            ['name' => 'create payment'],
            ['name' => 'read payment'],
            ['name' => 'update payment'],
            ['name' => 'delete payment'],
            ['name' => 'create grade'],
            ['name' => 'read grade'],
            ['name' => 'update grade'],
            ['name' => 'delete grade'],
            ['name' => 'create school settings'],
            ['name' => 'read school settings'],
            ['name' => 'update school settings'],
            ['name' => 'delete school settings'],
        ];

        foreach ($roles as $key => $role) {
            Role::create($role);
        }
    }
}
