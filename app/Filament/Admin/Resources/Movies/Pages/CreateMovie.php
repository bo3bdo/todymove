<?php

namespace App\Filament\Admin\Resources\Movies\Pages;

use App\Filament\Admin\Resources\Movies\MovieResource;
use App\Services\TmdbService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;

class CreateMovie extends CreateRecord
{
    protected static string $resource = MovieResource::class;

    /**
     * @var array<int, array{id: int, title: string, release_date: string|null, poster_path: string|null}>
     */
    public array $tmdbSearchResults = [];

    protected function getHeaderActions(): array
    {
        return [
            Action::make('fetchFromTmdb')
                ->label('Fetch from TMDB')
                ->icon('heroicon-o-cloud-arrow-down')
                ->action(function (): void {
                    $this->runFetchFromTmdb();
                }),
            Action::make('selectTmdbMovieModal')
                ->hidden()
                ->modalHeading('Select movie')
                ->modalSubmitAction(false)
                ->modalContent(fn (): View => view('filament.actions.tmdb-movie-list', [
                    'movies' => $this->tmdbSearchResults,
                ])),
        ];
    }

    /**
     * Run fetch logic (no modal). Opens selectTmdbMovieModal only when multiple results.
     */
    protected function runFetchFromTmdb(): void
    {
        $input = trim((string) ($this->form->getState()['title'] ?? ''));
        if (blank($input)) {
            Notification::make()
                ->danger()
                ->title('Paste a TMDB movie or TV series URL or enter a title')
                ->send();

            return;
        }

        $extracted = $this->extractTmdbIdFromInput($input);
        if ($extracted !== null) {
            if ($extracted['type'] === 'tv') {
                $details = app(TmdbService::class)->tvDetails($extracted['id']);
                if ($details) {
                    $this->fillFromTmdbTv($extracted['id']);
                    Notification::make()
                        ->success()
                        ->title('Fetched from TMDB (TV series)')
                        ->send();
                } else {
                    Notification::make()
                        ->danger()
                        ->title('TV series not found for this link or ID')
                        ->send();
                }
            } else {
                $details = app(TmdbService::class)->details($extracted['id']);
                if ($details) {
                    $this->fillFromTmdb($extracted['id']);
                    Notification::make()
                        ->success()
                        ->title('Fetched from TMDB')
                        ->send();
                } else {
                    Notification::make()
                        ->danger()
                        ->title('Movie not found for this link or ID')
                        ->send();
                }
            }

            return;
        }

        $results = app(TmdbService::class)->search($input);

        if (count($results) === 0) {
            Notification::make()
                ->danger()
                ->title('No results found for "'.$input.'"')
                ->send();

            return;
        }

        if (count($results) === 1) {
            $this->fillFromTmdb($results[0]['id']);
            Notification::make()
                ->success()
                ->title('Fetched from TMDB')
                ->send();

            return;
        }

        $this->tmdbSearchResults = $results;
        $this->mountAction('selectTmdbMovieModal');
    }

    /**
     * Extract TMDB ID and type from URL (movie or TV) or plain digits. Plain digits default to movie.
     *
     * @return array{type: 'movie'|'tv', id: int}|null
     */
    protected function extractTmdbIdFromInput(string $input): ?array
    {
        $input = trim($input);
        if (preg_match('#themoviedb\.org/tv/(\d+)#i', $input, $m)) {
            return ['type' => 'tv', 'id' => (int) $m[1]];
        }
        if (preg_match('#themoviedb\.org/movie/(\d+)#i', $input, $m)) {
            return ['type' => 'movie', 'id' => (int) $m[1]];
        }
        if (is_numeric($input)) {
            return ['type' => 'movie', 'id' => (int) $input];
        }

        return null;
    }

    public function selectTmdbMovie(int $tmdbId): void
    {
        $this->fillFromTmdb($tmdbId);
        Notification::make()
            ->success()
            ->title('Fetched from TMDB')
            ->send();
        $this->unmountAction();
    }

    protected function fillFromTmdb(int $tmdbId): void
    {
        $tmdb = app(TmdbService::class);
        $details = $tmdb->details($tmdbId);
        $videos = $tmdb->videos($tmdbId);

        if (! $details) {
            Notification::make()
                ->danger()
                ->title('Failed to fetch movie details')
                ->send();

            return;
        }

        $trailerKey = $tmdb->findBestTrailer($videos);
        $genres = $tmdb->genreNamesFromDetails($details);
        $releaseDate = isset($details['release_date']) && $details['release_date']
            ? Carbon::parse($details['release_date'])->format('Y-m-d')
            : null;

        $currentTitle = (string) ($this->form->getState()['title'] ?? '');
        $trimmed = trim($currentTitle);
        $isLinkOrId = is_numeric($trimmed) || str_contains(strtolower($currentTitle), 'themoviedb.org');
        $titleToUse = $isLinkOrId ? ($details['title'] ?? $currentTitle) : $currentTitle;

        $this->form->fill([
            'title' => $titleToUse,
            'tmdb_id' => $tmdbId,
            'poster_path' => $details['poster_path'] ?? null,
            'backdrop_path' => $details['backdrop_path'] ?? null,
            'overview' => $details['overview'] ?? null,
            'release_date' => $releaseDate,
            'runtime_minutes' => $details['runtime'] ?? null,
            'genres' => $genres,
            'vote_average' => $details['vote_average'] ?? null,
            'vote_count' => $details['vote_count'] ?? null,
            'trailer_youtube_key' => $trailerKey,
            'fetched_at' => now()->toDateTimeString(),
        ]);
    }

    protected function fillFromTmdbTv(int $tmdbId): void
    {
        $tmdb = app(TmdbService::class);
        $details = $tmdb->tvDetails($tmdbId);
        $videos = $tmdb->tvVideos($tmdbId);

        if (! $details) {
            Notification::make()
                ->danger()
                ->title('Failed to fetch TV series details')
                ->send();

            return;
        }

        $trailerKey = $tmdb->findBestTrailer($videos);
        $genres = $tmdb->genreNamesFromDetails($details);
        $firstAirDate = isset($details['first_air_date']) && $details['first_air_date']
            ? Carbon::parse($details['first_air_date'])->format('Y-m-d')
            : null;
        $episodeRunTime = $details['episode_run_time'][0] ?? null;

        $currentTitle = (string) ($this->form->getState()['title'] ?? '');
        $trimmed = trim($currentTitle);
        $isLinkOrId = is_numeric($trimmed) || str_contains(strtolower($currentTitle), 'themoviedb.org');
        $titleToUse = $isLinkOrId ? ($details['name'] ?? $currentTitle) : $currentTitle;

        $this->form->fill([
            'title' => $titleToUse,
            'tmdb_id' => $tmdbId,
            'poster_path' => $details['poster_path'] ?? null,
            'backdrop_path' => $details['backdrop_path'] ?? null,
            'overview' => $details['overview'] ?? null,
            'release_date' => $firstAirDate,
            'runtime_minutes' => $episodeRunTime,
            'genres' => $genres,
            'vote_average' => $details['vote_average'] ?? null,
            'vote_count' => $details['vote_count'] ?? null,
            'trailer_youtube_key' => $trailerKey,
            'fetched_at' => now()->toDateTimeString(),
        ]);
    }
}
