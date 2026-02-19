<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FilmOfWeekHistory extends Model
{
    protected $table = 'film_of_week_history';

    /**
     * @var list<string>
     */
    protected $fillable = ['movie_id'];

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }
}
