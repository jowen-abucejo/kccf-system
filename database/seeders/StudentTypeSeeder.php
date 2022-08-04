<?php

namespace Database\Seeders;

use App\Models\StudentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $student_types = [
            'OLD',
            'NEW',
            'RETURNEE',
            'SHIFTEE',
            'TRANSFEREE',
        ];

        foreach ($student_types as $student_type) {
            StudentType::create(['type' => $student_type]);
        }
    }
}
