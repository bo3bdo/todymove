<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Movie;
use App\Models\MovieVideoSource;
use App\Models\WeeklyPick;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalMovies = Movie::query()->count();
        $activeSources = MovieVideoSource::query()->where('is_active', true)->count();
        $moviesWithoutSource = Movie::query()
            ->whereDoesntHave('videoSources', fn ($q) => $q->where('is_active', true))
            ->count();
        $totalWeeklyPicks = WeeklyPick::query()->published()->count();

        return [
            Stat::make('إجمالي الأفلام', $totalMovies)
                ->description('في قاعدة البيانات')
                ->descriptionIcon('heroicon-m-film')
                ->color('primary')
                ->url(route('filament.admin.resources.movies.index')),

            Stat::make('مصادر الفيديو النشطة', $activeSources)
                ->description('مصادر قابلة للتشغيل')
                ->descriptionIcon('heroicon-m-play-circle')
                ->color('success'),

            Stat::make('أفلام بدون مصدر', $moviesWithoutSource)
                ->description($moviesWithoutSource > 0 ? 'تحتاج إضافة مصدر' : 'جميع الأفلام لديها مصادر')
                ->descriptionIcon($moviesWithoutSource > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($moviesWithoutSource > 0 ? 'danger' : 'success'),

            Stat::make('أسابيع منشورة', $totalWeeklyPicks)
                ->description('إجمالي أفلام الأسبوع')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
        ];
    }
}
