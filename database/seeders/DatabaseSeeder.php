<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            UserSeeder::class,
            TermSeeder::class,
            ProgramsAndLevelSeeder::class,
            StudentTypeSeeder::class,
            PermissionSeeder::class
        ]);
        $admin = User::create([
            'name' => 'admin admin',
            'email' => 'admin@kccf.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('Admin@123'),
        ]);
        $admin->assignRole('admin');
    }
}
