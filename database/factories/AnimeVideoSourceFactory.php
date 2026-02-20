<?php

namespace Database\Factories;

use App\Models\Anime;
use App\Models\AnimeVideoSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AnimeVideoSource>
 */
class AnimeVideoSourceFactory extends Factory
{
    protected $model = AnimeVideoSource::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'anime_id' => Anime::factory(),
            'label' => fake()->randomElement(['Server 1', 'Server 2', 'Backup']).' - '.fake()->randomElement(['720p', '1080p']),
            'url' => 'https://example.com/video.mp4',
            'type' => 'mp4',
            'quality' => '1080p',
            'priority' => fake()->numberBetween(1, 5),
            'is_active' => true,
        ];
    }
}
