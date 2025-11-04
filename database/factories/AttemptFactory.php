<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Attempt;
use App\Models\Competition;
use App\Models\Competitor;
use App\Models\Stage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attempt>
 */
class AttemptFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Attempt::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startedAt = fake()->dateTimeBetween('-1 hour', 'now');
        $durationSeconds = fake()->numberBetween(30, 120);
        $endedAt = (clone $startedAt)->modify("+{$durationSeconds} seconds");

        return [
            'competition_id' => Competition::factory(),
            'competitor_id' => Competitor::factory(),
            'stage_id' => Stage::factory(),
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
            'duration_ms' => $durationSeconds * 1000,
        ];
    }

    /**
     * Indicate that the attempt has not been started yet.
     */
    public function notStarted(): static
    {
        return $this->state(fn (array $attributes) => [
            'started_at' => null,
            'ended_at' => null,
            'duration_ms' => null,
        ]);
    }
}
