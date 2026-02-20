<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AnimeOfWeekController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $animeOfWeek = Anime::query()
            ->animeOfWeek()
            ->get();

        $archivePreview = Anime::query()
            ->where('is_anime_of_week', false)
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        return Inertia::render('anime-of-week', [
            'animeOfWeek' => $animeOfWeek->map(fn (Anime $a) => [
                'id' => $a->id,
                'title' => $a->title,
                'poster_url' => $a->poster_url,
                'trailer_url' => $a->trailer_url,
                'release_date' => $a->release_date?->format('Y'),
                'vote_average' => $a->vote_average,
            ])->values()->all(),
            'archivePreview' => $archivePreview->map(fn (Anime $a) => [
                'id' => $a->id,
                'title' => $a->title,
                'poster_url' => $a->poster_url,
            ])->values()->all(),
        ]);
    }
}
