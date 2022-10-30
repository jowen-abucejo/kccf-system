<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\ProgramLevel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProgramsAndLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $program_levels = [
            ['code' => 'PGS', 'description' => 'KINDERGARTEN'],
            ['code' => 'GS', 'description' =>  'GRADE SCHOOL'],
            ['code' => 'SGS', 'description' =>  'JUNIOR HIGH SCHOOL'],
            ['code' => 'SHS', 'description' =>  'SENIOR HIGH SCHOOL'],
            ['code' => 'COL', 'description' =>  'COLLEGE'],
        ];

        $levels = [
            ['code' => 'K1', 'description' =>  'KINDERGARTEN1'],
            ['code' => 'K2', 'description' =>  'KINDERGARTEN2'],

            ['code' => 'G01', 'description' =>  'GRADE 1'],
            ['code' => 'G02', 'description' =>  'GRADE 2'],
            ['code' => 'G03', 'description' =>  'GRADE 3'],
            ['code' => 'G04', 'description' =>  'GRADE 4'],
            ['code' => 'G05', 'description' =>  'GRADE 5'],
            ['code' => 'G06', 'description' =>  'GRADE 6'],

            ['code' => 'G07', 'description' =>  'GRADE 7'],
            ['code' => 'G08', 'description' =>  'GRADE 8'],
            ['code' => 'G09', 'description' =>  'GRADE 9'],
            ['code' => 'G10', 'description' =>  'GRADE 10'],

            ['code' => 'G11', 'description' =>  'GRADE 11'],
            ['code' => 'G12', 'description' =>  'GRADE 12'],

            ['code' => '1', 'description' =>  'FIRST YEAR'],
            ['code' => '2', 'description' =>  'SECOND YEAR'],
            ['code' => '3', 'description' =>  'THIRD YEAR'],
            ['code' => '4', 'description' =>  'FOURTH YEAR'],
        ];

        $programs = [
            ['program_level_id' => 5, 'code' => 'BSIT', 'description' => 'BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY'],
            ['program_level_id' => 5, 'code' => 'BSCRIM', 'description' => 'BACHELOR OF SCIENCE IN CRIMINOLOGY'],
            ['program_level_id' => 5, 'code' => 'BSBA', 'description' => 'BACHELOR OF SCIENCE IN BUSINESS ADMINISTRATION'],
            ['program_level_id' => 5, 'code' => 'BSE', 'description' => 'BACHELOR OF SCIENCE IN SECONDARY EDUCATION MAJOR IN ENGLISH'],
            ['program_level_id' => 4, 'code' => 'SMAW', 'description' => 'SHIELDED METAL ARC WELDING'],
            ['program_level_id' => 4, 'code' => 'TVL', 'description' => 'TECHNICAL-VOCATIONAL-LIVELIHOOD'],
            ['program_level_id' => 4, 'code' => 'GAS', 'description' => 'GENERAL ACADEMIC STRAND'],
            ['program_level_id' => 3, 'code' => 'SGS', 'description' => 'JUNIOR HIGH SCHOOL'],
            ['program_level_id' => 2, 'code' => 'GS', 'description' => 'GRADE SCHOOL'],
            ['program_level_id' => 1, 'code' => 'PGS', 'description' => 'PRE-SCHOOL']
        ];

        $program_level = null;
        for ($i = 0; $i < count($levels); $i++) {
            if ($i == 0) {
                $program_level = ProgramLevel::create($program_levels[0]);
            } elseif ($i == 2) {
                $program_level = ProgramLevel::create($program_levels[5]);
            } elseif ($i == 8) {
                $program_level = ProgramLevel::create($program_levels[2]);
            } elseif ($i == 12) {
                $program_level = ProgramLevel::create($program_levels[3]);
            } else if ($i == 14) {
                $program_level = ProgramLevel::create($program_levels[4]);
            }
            $program_level->levels()->create($levels[$i]);
        }

        foreach ($programs as $program) {
            Program::create($program);
        }
    }
}
