<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TmdbService
{
    private const BASE_URL = 'https://api.themoviedb.org/3';

    private const CACHE_TTL_SECONDS = 86400; // 24 hours

    public function __construct(
        private readonly string $accessToken = '',
        private readonly string $language = 'en-US'
    ) {}

    /**
     * Search movies by title. Returns array of results with id, title, release_date, poster_path.
     *
     * @return array<int, array{id: int, title: string, release_date: string|null, poster_path: string|null}>
     */
    public function search(string $query): array
    {
        $cacheKey = 'tmdb_search_'.md5($query);

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($query) {
            $response = Http::withToken($this->accessToken)
                ->get(self::BASE_URL.'/search/movie', [
                    'query' => $query,
                    'language' => $this->language,
                ]);

            if (! $response->successful()) {
                return [];
            }

            $data = $response->json();
            $results = $data['results'] ?? [];

            return array_map(function ($item) {
                return [
                    'id' => $item['id'],
                    'title' => $item['title'] ?? '',
                    'release_date' => $item['release_date'] ?? null,
                    'poster_path' => $item['poster_path'] ?? null,
                ];
            }, $results);
        });
    }

    /**
     * Get movie details by TMDB id.
     *
     * @return array<string, mixed>|null
     */
    public function details(int $tmdbId): ?array
    {
        $cacheKey = "tmdb_details_{$tmdbId}";

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($tmdbId) {
            $response = Http::withToken($this->accessToken)
                ->get(self::BASE_URL."/movie/{$tmdbId}", [
                    'language' => $this->language,
                ]);

            if (! $response->successful()) {
                return null;
            }

            return $response->json();
        });
    }

    /**
     * Get movie videos (trailers, teasers) by TMDB id.
     *
     * @return array<int, array<string, mixed>>
     */
    public function videos(int $tmdbId): array
    {
        $cacheKey = "tmdb_videos_{$tmdbId}";

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($tmdbId) {
            $response = Http::withToken($this->accessToken)
                ->get(self::BASE_URL."/movie/{$tmdbId}/videos", [
                    'language' => $this->language,
                ]);

            if (! $response->successful()) {
                return [];
            }

            $data = $response->json();

            return $data['results'] ?? [];
        });
    }

    /**
     * Pick the best YouTube trailer key from videos list. Prefers type=Trailer and site=YouTube.
     */
    public function findBestTrailer(array $videos): ?string
    {
        $trailers = array_filter($videos, function ($v) {
            $site = $v['site'] ?? '';
            $type = $v['type'] ?? '';

            return strtolower($site) === 'youtube' && strtolower($type) === 'trailer';
        });

        $first = reset($trailers);

        return $first['key'] ?? null;
    }

    /**
     * Extract genre names from TMDB details response.
     *
     * @param  array<string, mixed>  $details
     * @return list<string>
     */
    public function genreNamesFromDetails(array $details): array
    {
        $genres = $details['genres'] ?? [];

        return array_map(fn ($g) => $g['name'] ?? '', $genres);
    }
}
