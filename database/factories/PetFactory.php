<?php

namespace Database\Factories;

use App\Models\Species;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pet>
 */
class PetFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::query()->numberBetween(1, User::count())->value('id') ?? User::factory(),
            'species_id' => Species::query()->numberBetween(1, Species::count())->value('id')
                ?? Species::query()->firstOrCreate(['name' => 'อื่น ๆ'])->id,
            'breed_id' => null,
            'name' => $this->faker->firstName(),
            'color' => $this->faker->safeColorName(),
            'gender' => $this->faker->randomElement(['male', 'female', 'unknown']),
            'age' => $this->faker->optional()->numberBetween(0, 20),
            'description' => $this->faker->optional()->sentence(),
        ];
    }
}
