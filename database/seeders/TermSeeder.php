<?php

namespace Database\Seeders;

use App\Models\Term;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $terms = [
            ['code' => '1ST'],
            ['code' => '2ND'],
            ['code' => '3RD'],
            ['code' => 'SUMMER'],
        ];

        foreach ($terms as $term) {
            Term::create($term);
        }
    }
}
