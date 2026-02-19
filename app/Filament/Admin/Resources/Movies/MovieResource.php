<?php

namespace App\Filament\Admin\Resources\Movies;

use App\Filament\Admin\Resources\Movies\Pages\CreateMovie;
use App\Filament\Admin\Resources\Movies\Pages\EditMovie;
use App\Filament\Admin\Resources\Movies\Pages\ListMovies;
use App\Filament\Admin\Resources\Movies\Schemas\MovieForm;
use App\Filament\Admin\Resources\Movies\Tables\MoviesTable;
use App\Models\Movie;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MovieResource extends Resource
{
    protected static ?string $model = Movie::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return MovieForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MoviesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMovies::route('/'),
            'create' => CreateMovie::route('/create'),
            'edit' => EditMovie::route('/{record}/edit'),
        ];
    }
}
