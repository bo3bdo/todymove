<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Movie extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'tmdb_id',
        'title',
        'poster_path',
        'backdrop_path',
        'overview',
        'release_date',
        'runtime_minutes',
        'genres',
        'vote_average',
        'vote_count',
        'trailer_youtube_key',
        'fetched_at',
        'is_film_of_week',
        'film_of_week_order',
    ];

    /**
     * @var list<string>
     */
    protected $appends = [
        'poster_url',
        'trailer_url',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'genres' => 'array',
            'release_date' => 'date',
            'vote_average' => 'decimal:1',
            'fetched_at' => 'datetime',
            'is_film_of_week' => 'boolean',
        ];
    }

    public function scopeFilmOfWeek(Builder $query): Builder
    {
        return $query->where('is_film_of_week', true)->orderBy('film_of_week_order');
    }

    public function videoSources(): HasMany
    {
        return $this->hasMany(MovieVideoSource::class)->orderBy('priority');
    }

    public function getPosterUrlAttribute(): ?string
    {
        if (! $this->poster_path) {
            return null;
        }

        return 'https://image.tmdb.org/t/p/w500'.$this->poster_path;
    }

    public function getTrailerUrlAttribute(): ?string
    {
        if (! $this->trailer_youtube_key) {
            return null;
        }

        return 'https://www.youtube.com/watch?v='.$this->trailer_youtube_key;
    }
}
