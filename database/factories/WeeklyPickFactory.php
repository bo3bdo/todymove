<?php

namespace Database\Factories;

use App\Models\Movie;
use App\Models\WeeklyPick;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WeeklyPick>
 */
class WeeklyPickFactory extends Factory
{
    protected $model = WeeklyPick::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $thursday = Movie::factory()->create();
        $friday = Movie::factory()->create();

        return [
            'week_start_date' => fake()->unique()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'thursday_movie_id' => $thursday->id,
            'friday_movie_id' => $friday->id,
            'published_at' => now(),
        ];
    }
}
