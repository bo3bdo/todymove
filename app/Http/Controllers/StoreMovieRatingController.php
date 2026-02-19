<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMovieRatingRequest;
use App\Models\Movie;
use Illuminate\Http\RedirectResponse;

class StoreMovieRatingController extends Controller
{
    public function __invoke(StoreMovieRatingRequest $request, Movie $movie): RedirectResponse
    {
        $voterIdentifier = $request->user()
            ? 'u'.$request->user()->id
            : 'g'.hash('sha256', $request->ip().config('app.key'));

        $movie->ratings()->updateOrCreate(
            ['voter_identifier' => $voterIdentifier],
            ['rating' => $request->validated('rating')]
        );

        return back()->with('ratingSuccess', true);
    }
}
