<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Movie;
use Filament\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestMovies extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'آخر الأفلام المضافة';

    public function table(Table $table): Table
    {
        return $table
            ->query(Movie::query()->latest()->limit(5))
            ->paginated(false)
            ->columns([
                ImageColumn::make('poster_url')
                    ->label('')
                    ->width(40)
                    ->height(56)
                    ->defaultImageUrl(null),

                TextColumn::make('title')
                    ->label('الفيلم')
                    ->searchable()
                    ->weight('semibold'),

                TextColumn::make('release_date')
                    ->label('السنة')
                    ->date('Y')
                    ->sortable(),

                TextColumn::make('vote_average')
                    ->label('التقييم')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 7 => 'success',
                        $state >= 5 => 'warning',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn ($state) => '⭐ '.number_format((float) $state, 1)),

                TextColumn::make('videoSources_count')
                    ->label('مصادر')
                    ->counts('videoSources')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),
            ])
            ->actions([
                Action::make('edit')
                    ->label('تعديل')
                    ->icon('heroicon-m-pencil')
                    ->url(fn (Movie $record) => route('filament.admin.resources.movies.edit', $record)),

                Action::make('watch')
                    ->label('معاينة')
                    ->icon('heroicon-m-play')
                    ->color('gray')
                    ->url(fn (Movie $record) => route('watch', $record))
                    ->openUrlInNewTab(),
            ]);
    }
}
