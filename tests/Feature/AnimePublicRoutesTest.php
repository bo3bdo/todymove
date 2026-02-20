<?php

use App\Models\Anime;
use Inertia\Testing\AssertableInertia as Assert;

test('anime of week page renders with anime of week and archive preview', function () {
    $response = $this->get(route('anime'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('anime-of-week')
        ->has('animeOfWeek')
        ->has('archivePreview'));
});

test('anime watch page returns 404 when anime not found', function () {
    $response = $this->get(route('anime.watch', ['anime' => 99999]));

    $response->assertNotFound();
});

test('anime watch page renders when anime exists', function () {
    $anime = Anime::factory()->create();

    $response = $this->get(route('anime.watch', ['anime' => $anime->id]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('anime-watch')
        ->has('anime')
        ->has('anime.title')
        ->has('anime.watch_links')
        ->has('anime.comments')
        ->has('anime.visitor_rating_average')
        ->has('anime.visitor_rating_count')
        ->has('anime.current_rating')
        ->has('commentStoreUrl')
        ->has('ratingStoreUrl'));
});

test('guest can store a comment on an anime', function () {
    $anime = Anime::factory()->create();

    $response = $this->post(route('anime.comments.store', $anime), [
        'body' => 'أنمي رائع!',
        'author_name' => 'زائر',
        'author_email' => 'visitor@example.com',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('anime_comments', [
        'anime_id' => $anime->id,
        'body' => 'أنمي رائع!',
        'author_name' => 'زائر',
        'author_email' => 'visitor@example.com',
        'user_id' => null,
    ]);
});

test('anime comment validation requires body and author name', function () {
    $anime = Anime::factory()->create();

    $response = $this->post(route('anime.comments.store', $anime), [
        'body' => '',
        'author_name' => '',
    ]);

    $response->assertSessionHasErrors(['body', 'author_name']);
});

test('guest can store a rating on an anime', function () {
    $anime = Anime::factory()->create();

    $response = $this->post(route('anime.ratings.store', $anime), [
        'rating' => 4,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('anime_ratings', [
        'anime_id' => $anime->id,
        'rating' => 4,
    ]);
});

test('anime rating validation requires 1-5', function () {
    $anime = Anime::factory()->create();

    $response = $this->post(route('anime.ratings.store', $anime), [
        'rating' => 10,
    ]);

    $response->assertSessionHasErrors(['rating']);
});

test('home page includes hasAnimeOfWeek prop', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('home')
        ->has('hasAnimeOfWeek')
        ->where('hasAnimeOfWeek', false));
});

test('home page hasAnimeOfWeek is true when anime of week exists', function () {
    Anime::factory()->create([
        'is_anime_of_week' => true,
        'anime_of_week_order' => 1,
    ]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('home')
        ->where('hasAnimeOfWeek', true));
});
