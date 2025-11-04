<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Attempt;
use App\Models\AttemptResult;
use App\Models\Measure;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AttemptResult>
 */
class AttemptResultFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AttemptResult::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate a realistic pour that's close to a common target (e.g., 50ml +/- 5ml)
        $baseTarget = 50.00;
        $variance = fake()->randomFloat(2, -5, 5);
        $pouredMl = $baseTarget + $variance;

        return [
            'attempt_id' => Attempt::factory(),
            'measure_id' => Measure::factory(),
            'poured_ml' => max(0, $pouredMl), // Ensure non-negative
        ];
    }
}
