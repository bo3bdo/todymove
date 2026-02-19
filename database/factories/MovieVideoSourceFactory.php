<?php

namespace Database\Factories;

use App\Models\Movie;
use App\Models\MovieVideoSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MovieVideoSource>
 */
class MovieVideoSourceFactory extends Factory
{
    protected $model = MovieVideoSource::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'movie_id' => Movie::factory(),
            'label' => fake()->randomElement(['Server 1', 'Server 2', 'Backup']).' - '.fake()->randomElement(['720p', '1080p']),
            'url' => 'https://example.com/video.mp4',
            'type' => 'mp4',
            'quality' => '1080p',
            'priority' => fake()->numberBetween(1, 5),
            'is_active' => true,
        ];
    }
}
