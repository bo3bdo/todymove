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
     * Search movies and TV series by title.
     *
     * @return array<int, array{id: int, media_type: 'movie'|'tv', title: string, release_date: string|null, poster_path: string|null}>
     */
    public function search(string $query): array
    {
        $cacheKey = 'tmdb_search_'.md5($query);

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($query) {
            $response = Http::withToken($this->accessToken)
                ->get(self::BASE_URL.'/search/multi', [
                    'query' => $query,
                    'language' => $this->language,
                ]);

            if (! $response->successful()) {
                return [];
            }

            $data = $response->json();
            $results = $data['results'] ?? [];
            $results = array_values(array_filter($results, function ($item): bool {
                return in_array(($item['media_type'] ?? null), ['movie', 'tv'], true);
            }));

            return array_map(function ($item) {
                $isTv = ($item['media_type'] ?? '') === 'tv';

                return [
                    'id' => $item['id'],
                    'media_type' => $isTv ? 'tv' : 'movie',
                    'title' => $isTv ? ($item['name'] ?? '') : ($item['title'] ?? ''),
                    'release_date' => $isTv
                        ? ($item['first_air_date'] ?? null)
                        : ($item['release_date'] ?? null),
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
     * Get TV series details by TMDB id.
     *
     * @return array<string, mixed>|null
     */
    public function tvDetails(int $tmdbId): ?array
    {
        $cacheKey = "tmdb_tv_details_{$tmdbId}";

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($tmdbId) {
            $response = Http::withToken($this->accessToken)
                ->get(self::BASE_URL."/tv/{$tmdbId}", [
                    'language' => $this->language,
                ]);

            if (! $response->successful()) {
                return null;
            }

            return $response->json();
        });
    }

    /**
     * Get TV series videos (trailers, teasers) by TMDB id.
     *
     * @return array<int, array<string, mixed>>
     */
    public function tvVideos(int $tmdbId): array
    {
        $cacheKey = "tmdb_tv_videos_{$tmdbId}";

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($tmdbId) {
            $response = Http::withToken($this->accessToken)
                ->get(self::BASE_URL."/tv/{$tmdbId}/videos", [
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
