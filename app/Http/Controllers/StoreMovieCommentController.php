<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMovieCommentRequest;
use App\Models\Movie;
use Illuminate\Http\RedirectResponse;

class StoreMovieCommentController extends Controller
{
    public function __invoke(StoreMovieCommentRequest $request, Movie $movie): RedirectResponse
    {
        $movie->comments()->create([
            'body' => $request->validated('body'),
            'author_name' => $request->validated('author_name'),
            'author_email' => $request->validated('author_email'),
            'user_id' => $request->user()?->id,
        ]);

        return back()->with('commentSuccess', true);
    }
}
