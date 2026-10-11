<?php

namespace Database\Factories;

use App\Models\Sighting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SightingPhoto>
 */
class SightingPhotoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'sighting_id' => Sighting::query()->inRandomOrder()->value('id') ?? Sighting::factory(),
            'photo' => 'https://placehold.co/640x480?text=Sighting+'.rawurlencode($this->faker->uuid()),
        ];
    }
}
