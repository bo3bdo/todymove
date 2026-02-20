<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use App\Models\AnimeVideoSource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AnimeWatchController extends Controller
{
    public function __invoke(Request $request, Anime $anime): Response
    {
        $anime->load([
            'videoSources' => fn ($q) => $q->where('is_active', true),
            'comments' => fn ($q) => $q->latest()->limit(100),
        ]);

        $watch_links = $anime->videoSources->map(fn (AnimeVideoSource $s) => [
            'id' => $s->id,
            'label' => $s->label,
            'url' => $s->url,
        ])->values()->all();

        $voterIdentifier = $request->user()
            ? 'u'.$request->user()->id
            : 'g'.hash('sha256', $request->ip().config('app.key'));

        $currentRating = $anime->ratings()
            ->where('voter_identifier', $voterIdentifier)
            ->first();

        return Inertia::render('anime-watch', [
            'anime' => [
                'id' => $anime->id,
                'title' => $anime->title,
                'overview' => $anime->overview,
                'poster_url' => $anime->poster_url,
                'trailer_url' => $anime->trailer_url,
                'release_date' => $anime->release_date?->format('Y'),
                'episodes_count' => $anime->episodes_count,
                'vote_average' => $anime->vote_average,
                'watch_links' => $watch_links,
                'comments' => $anime->comments->map(fn ($c) => [
                    'id' => $c->id,
                    'body' => $c->body,
                    'author_name' => $c->author_name,
                    'created_at' => $c->created_at->toIso8601String(),
                ])->values()->all(),
                'visitor_rating_average' => $anime->visitor_rating_average,
                'visitor_rating_count' => $anime->visitor_rating_count,
                'current_rating' => $currentRating?->rating,
            ],
            'commentStoreUrl' => url('/anime/'.$anime->id.'/comments'),
            'ratingStoreUrl' => url('/anime/'.$anime->id.'/ratings'),
        ]);
    }
}
