<?php

namespace Database\Factories;

use App\Models\Calon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Calon>
 */
class CalonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nomor' => fake()->unique()->numerify('##'),
            'nama' => fake()->name(),
            'kelas' => fake()->randomElement(['XI-1', 'XI-2', 'XII-1', 'XII-2']),
            'foto' => fake()->unique()->word().'.png',
            'visi' => fake()->paragraph(),
            'misi' => fake()->paragraph(),
        ];
    }
}
