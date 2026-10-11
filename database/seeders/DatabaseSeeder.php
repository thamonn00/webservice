<?php

namespace Database\Seeders;

use App\Models\Pet;
use App\Models\PetPhoto;
use App\Models\Report;
use App\Models\ReportPhoto;
use App\Models\Sighting;
use App\Models\SightingPhoto;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ProvinceSeeder::class,
            DistrictSeeder::class,
            SubdistrictSeeder::class,
            SpeciesSeeder::class,
            BreedSeeder::class,
        ]);


        \App\Models\Pet::factory(10)->create();
        \App\Models\Pet::factory(10)->create();
        \App\Models\Pet::factory(10)->create();

    }
}
