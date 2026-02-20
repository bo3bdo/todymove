<?php

namespace App\Filament\Admin\Resources\Animes\Pages;

use App\Filament\Admin\Resources\Animes\AnimeResource;
use App\Models\AnimeVideoSource;
use App\Services\MalService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateAnime extends CreateRecord
{
    protected static string $resource = AnimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('fetchFromMal')
                ->label('Fetch from MyAnimeList')
                ->icon('heroicon-o-cloud-arrow-down')
                ->action(function (): void {
                    $this->runFetchFromMal();
                }),
        ];
    }

    protected function runFetchFromMal(): void
    {
        $input = trim((string) ($this->form->getState()['title'] ?? ''));
        if (blank($input)) {
            Notification::make()
                ->danger()
                ->title('Paste a MyAnimeList anime URL or enter ID')
                ->send();

            return;
        }

        $malId = $this->extractMalIdFromInput($input);
        if ($malId === null) {
            Notification::make()
                ->danger()
                ->title('Invalid URL. Use a link like: myanimelist.net/anime/51818/...')
                ->send();

            return;
        }

        $data = app(MalService::class)->anime($malId);
        if (! $data) {
            Notification::make()
                ->danger()
                ->title('Anime not found for this link or ID')
                ->send();

            return;
        }

        $this->form->fill(array_merge($this->form->getState(), $data));
        Notification::make()
            ->success()
            ->title('Fetched from MyAnimeList')
            ->send();
    }

    protected function extractMalIdFromInput(string $input): ?int
    {
        $input = trim($input);
        if (preg_match('#myanimelist\.net/anime/(\d+)#i', $input, $m)) {
            return (int) $m[1];
        }
        if (is_numeric($input)) {
            return (int) $input;
        }

        return null;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $sources = $data['videoSources'] ?? [];

        if (count($sources) < 1) {
            throw ValidationException::withMessages([
                'videoSources' => ['أضف رابط مشاهدة واحد على الأقل.'],
            ]);
        }

        unset($data['videoSources']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $sources = $this->form->getState()['videoSources'] ?? [];

        foreach ($sources as $index => $item) {
            AnimeVideoSource::create([
                'anime_id' => $this->record->id,
                'label' => $item['label'] ?? '',
                'url' => $item['url'] ?? '',
                'type' => $item['type'] ?? 'external',
                'priority' => (int) ($item['priority'] ?? $index + 1),
                'is_active' => (bool) ($item['is_active'] ?? true),
            ]);
        }
    }
}
