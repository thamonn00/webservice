<?php

namespace Database\Seeders;

use App\Models\Species;
use Illuminate\Database\Seeder;

class SpeciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (['สุนัข', 'แมว', 'นก', 'กระต่าย', 'หนูแฮมสเตอร์', 'หมู', 'โค', 'อื่น ๆ'] as $name) {
            Species::firstOrCreate(['name' => $name]);
        }
    }
}
