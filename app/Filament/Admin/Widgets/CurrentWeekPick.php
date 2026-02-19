<?php

namespace App\Filament\Admin\Widgets;

use App\Models\WeeklyPick;
use Filament\Widgets\Widget;

class CurrentWeekPick extends Widget
{
    protected static ?int $sort = 2;

    protected string $view = 'filament.admin.widgets.current-week-pick';

    protected int|string|array $columnSpan = 'full';

    public function getWeeklyPick(): ?WeeklyPick
    {
        return WeeklyPick::query()
            ->with(['thursdayMovie', 'fridayMovie'])
            ->latest('week_start_date')
            ->first();
    }
}
