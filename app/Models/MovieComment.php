<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovieComment extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'movie_id',
        'body',
        'author_name',
        'author_email',
        'user_id',
    ];

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
