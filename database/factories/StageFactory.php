<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Competition;
use App\Models\Stage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stage>
 */
class StageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Stage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $stageNames = [
            'Speed Round',
            'Precision Challenge',
            'Blind Pour',
            'Volume Test',
            'Multi-Pour Challenge',
            'Timed Accuracy',
        ];

        return [
            'competition_id' => Competition::factory(),
            'order' => 1,
            'name' => fake()->optional(0.9)->randomElement($stageNames),
            'expected_seconds' => fake()->numberBetween(30, 120),
            'status' => fake()->randomElement(['pending', 'active', 'complete']),
        ];
    }

    /**
     * Indicate that the stage is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the stage is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the stage is complete.
     */
    public function complete(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'complete',
        ]);
    }
}
