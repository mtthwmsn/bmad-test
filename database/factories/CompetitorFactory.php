<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Competitor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Competitor>
 */
class CompetitorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Competitor::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        $barNames = [
            'The Speakeasy',
            'Cocktail Lounge',
            'The Mixing Glass',
            'Liquid Lab',
            'The Spirit Room',
            'Bar None',
            'The Pour House',
            'Shaken & Stirred',
        ];

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'bar_name' => fake()->optional(0.8)->randomElement($barNames),
            'instagram' => fake()->optional(0.7)->passthrough('@' . strtolower($firstName) . '_' . strtolower($lastName)),
            'diffords_profile' => fake()->optional(0.5)->url(),
        ];
    }
}
