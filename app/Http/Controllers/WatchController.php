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
        $movie->load(['videoSources' => fn ($q) => $q->where('is_active', true)]);

        $watch_links = $movie->videoSources->map(fn (MovieVideoSource $s) => [
            'id' => $s->id,
            'label' => $s->label,
            'url' => $s->url,
        ])->values()->all();

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
            ],
        ]);
    }
}
