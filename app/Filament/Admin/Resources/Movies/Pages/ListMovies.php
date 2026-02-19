<?php

namespace App\Filament\Admin\Resources\Movies\Pages;

use App\Filament\Admin\Resources\Movies\MovieResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListMovies extends ListRecords
{
    protected static string $resource = MovieResource::class;

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
