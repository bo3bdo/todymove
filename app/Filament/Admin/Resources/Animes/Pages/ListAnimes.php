<?php

namespace App\Filament\Admin\Resources\Animes\Pages;

use App\Filament\Admin\Resources\Animes\AnimeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListAnimes extends ListRecords
{
    protected static string $resource = AnimeResource::class;

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modal(false)
                ->url(AnimeResource::getUrl('create')),
        ];
    }
}
