<?php

namespace App\Filament\Admin\Resources\Movies\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
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
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g. Server 1 - 1080p'),
                                TextInput::make('url')
                                    ->url()
                                    ->required()
                                    ->maxLength(2048),
                                Select::make('type')
                                    ->options([
                                        'mp4' => 'MP4',
                                        'hls' => 'HLS',
                                    ])
                                    ->placeholder('Optional'),
                                TextInput::make('quality')
                                    ->maxLength(50)
                                    ->placeholder('e.g. 720p, 1080p'),
                                TextInput::make('priority')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1),
                                Toggle::make('is_active')
                                    ->default(true),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Add video source'),
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
