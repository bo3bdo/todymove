<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\MovieVideoSource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WatchController extends Controller
{
    public function __invoke(Request $request, Movie $movie): Response
    {
        $movie->load([
            'videoSources' => fn ($q) => $q->where('is_active', true),
            'comments' => fn ($q) => $q->latest()->limit(100),
        ]);

        $watch_links = $movie->videoSources->map(fn (MovieVideoSource $s) => [
            'id' => $s->id,
            'label' => $s->label,
            'url' => $s->url,
        ])->values()->all();

        $voterIdentifier = $request->user()
            ? 'u'.$request->user()->id
            : 'g'.hash('sha256', $request->ip().config('app.key'));

        $currentRating = $movie->ratings()
            ->where('voter_identifier', $voterIdentifier)
            ->first();

        return Inertia::render('watch', [
            'movie' => [
                'id' => $movie->id,
                'title' => $movie->title,
                'overview' => $movie->overview,
                'poster_url' => $movie->poster_url,
                'trailer_url' => $movie->trailer_url,
                'release_date' => $movie->release_date?->format('Y'),
                'runtime_minutes' => $movie->runtime_minutes,
                'vote_average' => $movie->vote_average,
                'watch_links' => $watch_links,
                'comments' => $movie->comments->map(fn ($c) => [
                    'id' => $c->id,
                    'body' => $c->body,
                    'author_name' => $c->author_name,
                    'created_at' => $c->created_at->toIso8601String(),
                ])->values()->all(),
                'visitor_rating_average' => $movie->visitor_rating_average,
                'visitor_rating_count' => $movie->visitor_rating_count,
                'current_rating' => $currentRating?->rating,
            ],
            'commentStoreUrl' => url('/watch/'.$movie->id.'/comments'),
            'ratingStoreUrl' => url('/watch/'.$movie->id.'/ratings'),
        ]);
    }
}
