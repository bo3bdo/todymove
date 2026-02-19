<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ArchiveController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $movies = Movie::query()
            ->where('is_film_of_week', false)
            ->orderByDesc('id')
            ->paginate(24)
            ->withQueryString();

        $props = [
            'movies' => $movies->through(fn (Movie $movie) => [
                'id' => $movie->id,
                'title' => $movie->title,
                'poster_url' => $movie->poster_url,
                'release_date' => $movie->release_date?->format('Y'),
                'vote_average' => $movie->vote_average,
            ]),
        ];

        return Inertia::render('archive', $props);
    }
}
