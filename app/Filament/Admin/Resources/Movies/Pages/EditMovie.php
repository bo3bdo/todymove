<?php

namespace App\Filament\Admin\Resources\Movies\Pages;

use App\Filament\Admin\Resources\Movies\MovieResource;
use App\Services\TmdbService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class EditMovie extends EditRecord
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
            DeleteAction::make(),
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
                ->title('Paste a TMDB movie URL or enter a title')
                ->send();

            return;
        }

        $tmdbId = $this->extractTmdbIdFromInput($input);
        if ($tmdbId !== null) {
            $details = app(TmdbService::class)->details($tmdbId);
            if ($details) {
                $this->fillFromTmdb($tmdbId);
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
     * Extract TMDB movie ID from a full URL (e.g. themoviedb.org/movie/1236153-mercy) or plain digits.
     */
    protected function extractTmdbIdFromInput(string $input): ?int
    {
        $input = trim($input);
        if (preg_match('#themoviedb\.org/movie/(\d+)#i', $input, $m)) {
            return (int) $m[1];
        }
        if (is_numeric($input)) {
            return (int) $input;
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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $sources = $data['videoSources'] ?? [];

        if (count($sources) < 1) {
            throw ValidationException::withMessages([
                'videoSources' => ['Add at least one active video source before saving.'],
            ]);
        }

        return $data;
    }
}
