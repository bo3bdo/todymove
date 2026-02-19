<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $filmOfWeek = Movie::query()
            ->filmOfWeek()
            ->get();

        $archivePreview = Movie::query()
            ->where('is_film_of_week', false)
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        return Inertia::render('home', [
            'filmOfWeek' => $filmOfWeek->map(fn (Movie $m) => [
                'id' => $m->id,
                'title' => $m->title,
                'poster_url' => $m->poster_url,
                'trailer_url' => $m->trailer_url,
                'release_date' => $m->release_date?->format('Y'),
                'vote_average' => $m->vote_average,
            ])->values()->all(),
            'archivePreview' => $archivePreview->map(fn (Movie $m) => [
                'id' => $m->id,
                'title' => $m->title,
                'poster_url' => $m->poster_url,
            ])->values()->all(),
        ]);
    }
}
