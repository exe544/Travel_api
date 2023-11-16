<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Travel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tour>
 */
class TourFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'name' => 'Tour on'. fake()->dayOfWeek . rand(1, 5),
            'starting_date' => now()->format('Y-m-d'),
            'ending_date' => now()->addDays(rand(1, 10))->format('Y-m-d'),
            'price' => fake()->randomFloat(2, 10, 999),
        ];
    }
}
