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
        ->has('movie.watch_links')
        ->has('movie.comments')
        ->has('movie.visitor_rating_average')
        ->has('movie.visitor_rating_count')
        ->has('movie.current_rating')
        ->has('commentStoreUrl')
        ->has('ratingStoreUrl'));
});

test('guest can store a comment on a movie', function () {
    $movie = Movie::factory()->create();

    $response = $this->post(route('watch.comments.store', $movie), [
        'body' => 'فيلم رائع!',
        'author_name' => 'زائر',
        'author_email' => 'visitor@example.com',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('movie_comments', [
        'movie_id' => $movie->id,
        'body' => 'فيلم رائع!',
        'author_name' => 'زائر',
        'author_email' => 'visitor@example.com',
        'user_id' => null,
    ]);
});

test('comment validation requires body and author name', function () {
    $movie = Movie::factory()->create();

    $response = $this->post(route('watch.comments.store', $movie), [
        'body' => '',
        'author_name' => '',
    ]);

    $response->assertSessionHasErrors(['body', 'author_name']);
});

test('guest can store a rating on a movie', function () {
    $movie = Movie::factory()->create();

    $response = $this->post(route('watch.ratings.store', $movie), [
        'rating' => 4,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('movie_ratings', [
        'movie_id' => $movie->id,
        'rating' => 4,
    ]);
});

test('rating validation requires 1-5', function () {
    $movie = Movie::factory()->create();

    $response = $this->post(route('watch.ratings.store', $movie), [
        'rating' => 10,
    ]);

    $response->assertSessionHasErrors(['rating']);
});
