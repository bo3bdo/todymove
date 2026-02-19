<?php

namespace App\Filament\Admin\Resources\Movies\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MovieForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Movie details')
                    ->schema([
                        TextInput::make('title')
                            ->label('TMDB link or title')
                            ->placeholder('e.g. https://www.themoviedb.org/movie/1236153-mercy')
                            ->helperText('Paste the full TMDB movie page URL (e.g. themoviedb.org/movie/1236153-mercy) or enter a title to search.')
                            ->required()
                            ->maxLength(500),
                        Repeater::make('videoSources')
                            ->relationship()
                            ->schema([
                                TextInput::make('label')
                                    ->label('الاسم')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('مثل: Netflix، Shahid، Weyyak'),
                                TextInput::make('url')
                                    ->label('رابط المشاهدة')
                                    ->url()
                                    ->required()
                                    ->maxLength(2048)
                                    ->placeholder('https://...')
                                    ->helperText('رابط صفحة الفيلم على الموقع (يفتح في تاب جديد)'),
                                TextInput::make('type')
                                    ->default('external')
                                    ->dehydrated()
                                    ->hidden(),
                                TextInput::make('priority')
                                    ->default(1)
                                    ->dehydrated()
                                    ->hidden(),
                                Toggle::make('is_active')
                                    ->default(true)
                                    ->hidden()
                                    ->dehydrated(),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('إضافة رابط مشاهدة'),
                    ]),
                Section::make('TMDB data')
                    ->description('Fetched automatically via "Fetch from TMDB" action.')
                    ->schema([
                        TextInput::make('poster_path')
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('backdrop_path')
                            ->disabled()
                            ->dehydrated(),
                        Textarea::make('overview')
                            ->disabled()
                            ->dehydrated()
                            ->columnSpanFull(),
                        DatePicker::make('release_date')
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('runtime_minutes')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),
                        TagsInput::make('genres')
                            ->disabled()
                            ->dehydrated()
                            ->columnSpanFull(),
                        TextInput::make('vote_average')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('vote_count')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('trailer_youtube_key')
                            ->disabled()
                            ->dehydrated(),
                        DateTimePicker::make('fetched_at')
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }
}
