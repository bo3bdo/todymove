<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnimeRatingRequest;
use App\Models\Anime;
use Illuminate\Http\RedirectResponse;

class StoreAnimeRatingController extends Controller
{
    public function __invoke(StoreAnimeRatingRequest $request, Anime $anime): RedirectResponse
    {
        $voterIdentifier = $request->user()
            ? 'u'.$request->user()->id
            : 'g'.hash('sha256', $request->ip().config('app.key'));

        $anime->ratings()->updateOrCreate(
            ['voter_identifier' => $voterIdentifier],
            ['rating' => $request->validated('rating')]
        );

        return back()->with('ratingSuccess', true);
    }
}
