<?php

use App\Models\Movie;
use Inertia\Testing\AssertableInertia as Assert;

test('home page renders with film of week and archive preview', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('home')
        ->has('filmOfWeek')
        ->has('archivePreview'));
});

test('archive page renders with paginated movies', function () {
    $response = $this->get(route('archive'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('archive')
        ->has('movies')
        ->has('movies.data')
        ->has('movies.current_page')
        ->has('movies.last_page'));
});

test('watch page returns 404 when movie not found', function () {
    $response = $this->get(route('watch', ['movie' => 99999]));

    $response->assertNotFound();
});

test('watch page renders when movie exists', function () {
    $movie = Movie::factory()->create();

    $response = $this->get(route('watch', ['movie' => $movie->id]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('watch')
        ->has('movie')
        ->has('movie.title')
        ->has('movie.watch_links'));
});
