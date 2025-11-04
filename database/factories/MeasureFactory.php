<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Measure;
use App\Models\Stage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Measure>
 */
class MeasureFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Measure::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $spirits = [
            'Vodka',
            'Gin',
            'Rum',
            'Tequila',
            'Whiskey',
            'Bourbon',
            'Brandy',
            'Vermouth',
        ];

        $commonPours = [25.00, 35.00, 50.00, 75.00, 100.00];

        return [
            'stage_id' => Stage::factory(),
            'name' => fake()->randomElement($spirits),
            'target_ml' => fake()->randomElement($commonPours),
            'order' => 1,
        ];
    }
}
