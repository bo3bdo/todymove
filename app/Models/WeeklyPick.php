<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyPick extends Model
{
    use HasFactory;
    /**
     * @var list<string>
     */
    protected $fillable = [
        'week_start_date',
        'thursday_movie_id',
        'friday_movie_id',
        'published_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'week_start_date' => 'date',
            'published_at' => 'datetime',
        ];
    }

    public function thursdayMovie(): BelongsTo
    {
        return $this->belongsTo(Movie::class, 'thursday_movie_id');
    }

    public function fridayMovie(): BelongsTo
    {
        return $this->belongsTo(Movie::class, 'friday_movie_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeCurrent(Builder $query): Builder
    {
        return $query->published()->orderByDesc('week_start_date')->limit(1);
    }

    public function scopeArchive(Builder $query): Builder
    {
        return $query->published()->orderByDesc('week_start_date');
    }
}
