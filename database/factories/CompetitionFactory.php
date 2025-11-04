<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Competition;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Competition>
 */
class CompetitionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Competition::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cities = ['London', 'New York', 'Paris', 'Tokyo', 'Sydney', 'Berlin', 'Barcelona', 'Amsterdam'];
        $city = fake()->randomElement($cities);

        return [
            'name' => $city . ' Pour Test ' . fake()->year(),
            'city' => $city,
            'country_code' => fake()->countryCode(),
            'date' => fake()->dateTimeBetween('now', '+6 months'),
            'status' => fake()->randomElement(['draft', 'active', 'completed']),
        ];
    }

    /**
     * Indicate that the competition is in draft status.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Indicate that the competition is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the competition is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }
}
