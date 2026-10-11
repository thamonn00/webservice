<?php

namespace Database\Factories;

use App\Models\Pet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PetPhoto>
 */
class PetPhotoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'pet_id' => Pet::query()->inRandomOrder()->value('id') ?? Pet::factory(),
            'photo' => 'https://placehold.co/640x480?text=Pet+'.rawurlencode($this->faker->uuid()),
            'is_primary' => false,
        ];
    }
}
