<?php

namespace App\Filament\Admin\Resources\Movies\Tables;

use App\Models\FilmOfWeekHistory;
use App\Models\Movie;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MoviesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('poster_path')
                    ->label('Poster')
                    ->getStateUsing(fn ($record) => $record->poster_path
                        ? 'https://image.tmdb.org/t/p/w92'.$record->poster_path
                        : null)
                    ->defaultImageUrl('https://via.placeholder.com/92x138?text=No+poster')
                    ->width(92)
                    ->height(138),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_film_of_week')
                    ->label('Film of week')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-minus'),
                TextColumn::make('release_date')
                    ->date()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state?->format('Y')),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('addToFilmOfWeek')
                    ->label('Add to film of week')
                    ->icon('heroicon-o-star')
                    ->iconButton()
                    ->visible(fn (Movie $record): bool => ! $record->is_film_of_week && Movie::filmOfWeek()->count() < 4)
                    ->action(function (Movie $record): void {
                        $usedOrders = Movie::filmOfWeek()->pluck('film_of_week_order')->all();
                        $order = 1;
                        while (in_array($order, $usedOrders, true)) {
                            $order++;
                        }
                        $record->update([
                            'is_film_of_week' => true,
                            'film_of_week_order' => $order,
                        ]);
                        FilmOfWeekHistory::create(['movie_id' => $record->id]);

                        Notification::make()
                            ->title('Added to film of the week')
                            ->success()
                            ->send();
                    }),
                Action::make('removeFromFilmOfWeek')
                    ->label('Remove from film of week')
                    ->icon('heroicon-o-minus-circle')
                    ->iconButton()
                    ->color('gray')
                    ->visible(fn (Movie $record): bool => (bool) $record->is_film_of_week)
                    ->action(function (Movie $record): void {
                        $order = $record->film_of_week_order;
                        $record->update([
                            'is_film_of_week' => false,
                            'film_of_week_order' => null,
                        ]);
                        Movie::filmOfWeek()->where('film_of_week_order', '>', $order)->get()->each(function (Movie $m): void {
                            $m->update(['film_of_week_order' => $m->film_of_week_order - 1]);
                        });

                        Notification::make()
                            ->title('Removed from film of the week')
                            ->success()
                            ->send();
                    }),
                EditAction::make()
                    ->iconButton(),
                DeleteAction::make()
                    ->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
