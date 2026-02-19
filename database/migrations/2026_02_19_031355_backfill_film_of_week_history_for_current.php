<?php

use App\Models\FilmOfWeekHistory;
use App\Models\Movie;
use App\Models\WeeklyPick;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Backfill film_of_week_history so current "film of week" and old weekly picks appear in archive.
     */
    public function up(): void
    {
        $existingIds = FilmOfWeekHistory::query()->distinct()->pluck('movie_id')->all();

        foreach (Movie::query()->where('is_film_of_week', true)->whereNotIn('id', $existingIds)->pluck('id') as $movieId) {
            FilmOfWeekHistory::create(['movie_id' => $movieId]);
        }

        if (\Illuminate\Support\Facades\Schema::hasTable('weekly_picks')) {
            $fromPicks = WeeklyPick::query()
                ->published()
                ->get()
                ->flatMap(fn ($p) => [$p->thursday_movie_id, $p->friday_movie_id])
                ->filter()
                ->unique()
                ->values();
            foreach ($fromPicks as $movieId) {
                if (! in_array($movieId, $existingIds, true)) {
                    FilmOfWeekHistory::create(['movie_id' => $movieId]);
                    $existingIds[] = $movieId;
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reliably remove only the backfilled rows; leave history as is.
    }
};
