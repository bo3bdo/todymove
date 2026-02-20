<?php

namespace Database\Factories;

use App\Models\Anime;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Anime>
 */
class AnimeFactory extends Factory
{
    protected $model = Anime::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'overview' => fake()->paragraph(),
            'release_date' => fake()->date(),
            'episodes_count' => fake()->numberBetween(12, 24),
            'genres' => ['Action', 'Adventure'],
            'vote_average' => fake()->randomFloat(1, 6, 9),
            'vote_count' => fake()->numberBetween(100, 10000),
        ];
    }
}
