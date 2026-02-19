<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovieRating extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'movie_id',
        'rating',
        'voter_identifier',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
    }

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }
}
