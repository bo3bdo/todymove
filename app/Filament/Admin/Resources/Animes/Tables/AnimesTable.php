<?php

namespace App\Filament\Admin\Resources\Animes\Tables;

use App\Models\Anime;
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

class AnimesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('poster_url')
                    ->label('Poster')
                    ->getStateUsing(fn (Anime $record) => $record->poster_url)
                    ->defaultImageUrl('https://via.placeholder.com/92x138?text=No+poster')
                    ->width(92)
                    ->height(138),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_anime_of_week')
                    ->label('Anime of week')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-minus'),
                TextColumn::make('release_date')
                    ->date()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state?->format('Y')),
                TextColumn::make('episodes_count')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('addToAnimeOfWeek')
                    ->label('Add to anime of week')
                    ->icon('heroicon-o-star')
                    ->iconButton()
                    ->visible(fn (Anime $record): bool => ! $record->is_anime_of_week && Anime::animeOfWeek()->count() < 4)
                    ->action(function (Anime $record): void {
                        $usedOrders = Anime::animeOfWeek()->pluck('anime_of_week_order')->all();
                        $order = 1;
                        while (in_array($order, $usedOrders, true)) {
                            $order++;
                        }
                        $record->update([
                            'is_anime_of_week' => true,
                            'anime_of_week_order' => $order,
                        ]);

                        Notification::make()
                            ->title('Added to anime of the week')
                            ->success()
                            ->send();
                    }),
                Action::make('removeFromAnimeOfWeek')
                    ->label('Remove from anime of week')
                    ->icon('heroicon-o-minus-circle')
                    ->iconButton()
                    ->color('gray')
                    ->visible(fn (Anime $record): bool => (bool) $record->is_anime_of_week)
                    ->action(function (Anime $record): void {
                        $order = $record->anime_of_week_order;
                        $record->update([
                            'is_anime_of_week' => false,
                            'anime_of_week_order' => null,
                        ]);
                        Anime::animeOfWeek()->where('anime_of_week_order', '>', $order)->get()->each(function (Anime $a): void {
                            $a->update(['anime_of_week_order' => $a->anime_of_week_order - 1]);
                        });

                        Notification::make()
                            ->title('Removed from anime of the week')
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
