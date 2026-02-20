<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnimeCommentRequest;
use App\Models\Anime;
use Illuminate\Http\RedirectResponse;

class StoreAnimeCommentController extends Controller
{
    public function __invoke(StoreAnimeCommentRequest $request, Anime $anime): RedirectResponse
    {
        $anime->comments()->create([
            'body' => $request->validated('body'),
            'author_name' => $request->validated('author_name'),
            'author_email' => $request->validated('author_email'),
            'user_id' => $request->user()?->id,
        ]);

        return back()->with('commentSuccess', true);
    }
}
