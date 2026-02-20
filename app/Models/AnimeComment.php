<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnimeComment extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'anime_id',
        'body',
        'author_name',
        'author_email',
        'user_id',
    ];

    public function anime(): BelongsTo
    {
        return $this->belongsTo(Anime::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
