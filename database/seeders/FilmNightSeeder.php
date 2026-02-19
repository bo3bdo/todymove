<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\MovieVideoSource;
use App\Models\WeeklyPick;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FilmNightSeeder extends Seeder
{
    /**
     * Realistic movie data for testing (TMDB IDs, poster paths, trailers, overviews).
     *
     * @return array<int, array<string, mixed>>
     */
    private function moviesData(): array
    {
        return [
            1 => [
                'tmdb_id' => 278,
                'title' => 'The Shawshank Redemption',
                'poster_path' => '/q6y0Go1tsGEsmtFryDOJo3dEmqu.jpg',
                'backdrop_path' => '/kXfqcdQKsToO0OUXHcrrNCHDBzO.jpg',
                'overview' => 'Framed in the 1940s for the double murder of his wife and her lover, upstanding banker Andy Dufresne begins a new life at the Shawshank prison, where he puts his accounting skills to work for an amoral warden. During his long stretch in prison, Dufresne comes to be admired by the other inmates.',
                'release_date' => '1994-09-23',
                'runtime_minutes' => 142,
                'genres' => ['Drama', 'Crime'],
                'vote_average' => 8.7,
                'vote_count' => 25000,
                'trailer_youtube_key' => '6hB3S9bIcoA',
                'fetched_at' => now(),
                'sources' => [
                    ['label' => 'Server 1 - 1080p', 'url' => 'https://example.com/shawshank-1080p.mp4', 'type' => 'mp4', 'quality' => '1080p', 'priority' => 1],
                    ['label' => 'Server 2 - 720p', 'url' => 'https://example.com/shawshank-720p.mp4', 'type' => 'mp4', 'quality' => '720p', 'priority' => 2],
                ],
            ],
            2 => [
                'tmdb_id' => 155,
                'title' => 'The Dark Knight',
                'poster_path' => '/qJ2tW6WMUDux911r6m7haRef0WH.jpg',
                'backdrop_path' => '/hkBaDkMWbLaf8B1lsrKz3RkG4qG.jpg',
                'overview' => 'Batman raises the stakes in his war on crime. With the help of Lt. Gordon and District Attorney Harvey Dent, Batman sets out to dismantle the remaining criminal organizations that plague the streets.',
                'release_date' => '2008-07-16',
                'runtime_minutes' => 152,
                'genres' => ['Action', 'Crime', 'Drama'],
                'vote_average' => 9.0,
                'vote_count' => 32000,
                'trailer_youtube_key' => 'EXeTwQWrcwY',
                'fetched_at' => now(),
                'sources' => [
                    ['label' => 'Server 1 - 1080p', 'url' => 'https://example.com/dark-knight-1080p.mp4', 'type' => 'mp4', 'quality' => '1080p', 'priority' => 1],
                    ['label' => 'Backup - 720p', 'url' => 'https://example.com/dark-knight-720p.mp4', 'type' => 'mp4', 'quality' => '720p', 'priority' => 2],
                ],
            ],
            3 => [
                'tmdb_id' => 27205,
                'title' => 'Inception',
                'poster_path' => '/9gk7adHYeDvHkCSEqAvQNLV5uq4.jpg',
                'backdrop_path' => '/s3TBrRGB1iav7gFOCNx3H31MoES.jpg',
                'overview' => 'Cobb, a skilled thief who commits corporate espionage by infiltrating the subconscious of his targets, is offered a chance to have his criminal record erased if he can pull off the reverse: an inception.',
                'release_date' => '2010-07-15',
                'runtime_minutes' => 148,
                'genres' => ['Action', 'Science Fiction', 'Adventure'],
                'vote_average' => 8.4,
                'vote_count' => 35000,
                'trailer_youtube_key' => 'YoHD9XEInc0',
                'fetched_at' => now(),
                'sources' => [
                    ['label' => 'Server 1 - 1080p', 'url' => 'https://example.com/inception-1080p.mp4', 'type' => 'mp4', 'quality' => '1080p', 'priority' => 1],
                    ['label' => 'Server 2 - 720p', 'url' => 'https://example.com/inception-720p.mp4', 'type' => 'mp4', 'quality' => '720p', 'priority' => 2],
                ],
            ],
            4 => [
                'tmdb_id' => 157336,
                'title' => 'Interstellar',
                'poster_path' => '/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg',
                'backdrop_path' => '/xu9zaAevzQ5nnrsXN6JcahLnG4i.jpg',
                'overview' => 'The adventures of a group of explorers who make use of a newly discovered wormhole to surpass the limitations on human space travel and conquer the vast distances involved in an interstellar voyage.',
                'release_date' => '2014-11-05',
                'runtime_minutes' => 169,
                'genres' => ['Adventure', 'Drama', 'Science Fiction'],
                'vote_average' => 8.7,
                'vote_count' => 33000,
                'trailer_youtube_key' => 'zSWdZVtN7eE',
                'fetched_at' => now(),
                'sources' => [
                    ['label' => 'Server 1 - 1080p', 'url' => 'https://example.com/interstellar-1080p.mp4', 'type' => 'mp4', 'quality' => '1080p', 'priority' => 1],
                ],
            ],
            5 => [
                'tmdb_id' => 238,
                'title' => 'The Godfather',
                'poster_path' => '/3bhkrj58Vtu7enYsRolD1fZdja1.jpg',
                'backdrop_path' => '/tmU7GeKVybMWFButWEGl2M4GeiP.jpg',
                'overview' => 'Spanning the years 1945 to 1955, a chronicle of the fictional Italian-American Corleone crime family. When organized crime family patriarch Vito Corleone barely survives an attempt on his life, his youngest son Michael is drawn into a bloody war.',
                'release_date' => '1972-03-14',
                'runtime_minutes' => 175,
                'genres' => ['Drama', 'Crime'],
                'vote_average' => 8.7,
                'vote_count' => 24000,
                'trailer_youtube_key' => 'sY1S34973zA',
                'fetched_at' => now(),
                'sources' => [
                    ['label' => 'Server 1 - 1080p', 'url' => 'https://example.com/godfather-1080p.mp4', 'type' => 'mp4', 'quality' => '1080p', 'priority' => 1],
                    ['label' => 'Server 2 - 720p', 'url' => 'https://example.com/godfather-720p.mp4', 'type' => 'mp4', 'quality' => '720p', 'priority' => 2],
                ],
            ],
            6 => [
                'tmdb_id' => 680,
                'title' => 'Pulp Fiction',
                'poster_path' => '/fIE3lAGcZDV1G6XM5KmuWnNsPp1.jpg',
                'backdrop_path' => '/suaEOtk1N1gq4xAw5aCbIECaFOb.jpg',
                'overview' => 'A burger-loving hit man, his philosophical partner, a drug-addled gangster\'s moll and a washed-up boxer converge in this sprawling, comedic crime caper. Their adventures unfurl in three stories that ingeniously trip back and forth in time.',
                'release_date' => '1994-09-10',
                'runtime_minutes' => 154,
                'genres' => ['Thriller', 'Crime'],
                'vote_average' => 8.5,
                'vote_count' => 26000,
                'trailer_youtube_key' => 's7EdQ4FqbhY',
                'fetched_at' => now(),
                'sources' => [
                    ['label' => 'Server 1 - 1080p', 'url' => 'https://example.com/pulp-fiction-1080p.mp4', 'type' => 'mp4', 'quality' => '1080p', 'priority' => 1],
                ],
            ],
            7 => [
                'tmdb_id' => 550,
                'title' => 'Fight Club',
                'poster_path' => '/pB8BM7pdSp6B6Ih7QZ4DrQ3PmJK.jpg',
                'backdrop_path' => '/fCayJrkfRaCRCTh8GqN30f8oyQF.jpg',
                'overview' => 'A ticking-time-bomb insomniac and a slippery soap salesman channel primal male aggression into a shocking new form of therapy. Their concept catches on, with underground "fight clubs" forming in every town.',
                'release_date' => '1999-10-14',
                'runtime_minutes' => 139,
                'genres' => ['Drama'],
                'vote_average' => 8.4,
                'vote_count' => 28000,
                'trailer_youtube_key' => 'SUXWAEX2jlg',
                'fetched_at' => now(),
                'sources' => [
                    ['label' => 'Server 1 - 1080p', 'url' => 'https://example.com/fight-club-1080p.mp4', 'type' => 'mp4', 'quality' => '1080p', 'priority' => 1],
                    ['label' => 'Backup', 'url' => 'https://example.com/fight-club-backup.mp4', 'type' => 'mp4', 'quality' => '720p', 'priority' => 2],
                ],
            ],
            8 => [
                'tmdb_id' => 496243,
                'title' => 'Parasite',
                'poster_path' => '/7IiTTgkokJ3EcaBEfWJyTUrykGo.jpg',
                'backdrop_path' => '/TU9NIjwzjoKPwQHoHshkFcQUCG7.jpg',
                'overview' => 'All unemployed, Ki-taek and his family take peculiar interest in the wealthy and glamorous Parks. They infiltrate the Parks\' home and become entangled in an unexpected incident.',
                'release_date' => '2019-05-30',
                'runtime_minutes' => 133,
                'genres' => ['Comedy', 'Thriller', 'Drama'],
                'vote_average' => 8.5,
                'vote_count' => 17000,
                'trailer_youtube_key' => '5xH0HfJHsaY',
                'fetched_at' => now(),
                'sources' => [
                    ['label' => 'Server 1 - 1080p', 'url' => 'https://example.com/parasite-1080p.mp4', 'type' => 'mp4', 'quality' => '1080p', 'priority' => 1],
                    ['label' => 'Server 2 - 720p', 'url' => 'https://example.com/parasite-720p.mp4', 'type' => 'mp4', 'quality' => '720p', 'priority' => 2],
                ],
            ],
            9 => [
                'tmdb_id' => 693134,
                'title' => 'Dune: Part Two',
                'poster_path' => '/8b8R8l88Qje9dn9OE8PY05Nxl1X.jpg',
                'backdrop_path' => '/xOMo8BRK7PfcJvqwJCbxOWsYvnD.jpg',
                'overview' => 'Paul Atreides unites with Chani and the Fremen while seeking revenge against the conspirators who destroyed his family.',
                'release_date' => '2024-02-27',
                'runtime_minutes' => 167,
                'genres' => ['Science Fiction', 'Adventure'],
                'vote_average' => 8.1,
                'vote_count' => 8500,
                'trailer_youtube_key' => 'WBKEg2FQc9I',
                'fetched_at' => now(),
                'sources' => [
                    ['label' => 'Server 1 - 1080p', 'url' => 'https://example.com/dune2-1080p.mp4', 'type' => 'mp4', 'quality' => '1080p', 'priority' => 1],
                    ['label' => 'Server 2 - 720p', 'url' => 'https://example.com/dune2-720p.mp4', 'type' => 'mp4', 'quality' => '720p', 'priority' => 2],
                ],
            ],
            10 => [
                'tmdb_id' => 872585,
                'title' => 'Oppenheimer',
                'poster_path' => '/8Gxv8gSFCU0XGDykEGv7zR1n2ua.jpg',
                'backdrop_path' => '/f4E0ocYeToUmX1zkPh0HtDuGzzd.jpg',
                'overview' => 'The story of J. Robert Oppenheimer\'s role in the development of the atomic bomb during World War II.',
                'release_date' => '2023-07-19',
                'runtime_minutes' => 181,
                'genres' => ['Drama', 'History'],
                'vote_average' => 8.1,
                'vote_count' => 12000,
                'trailer_youtube_key' => 'uYPbbksJxIg',
                'fetched_at' => now(),
                'sources' => [
                    ['label' => 'Server 1 - 1080p', 'url' => 'https://example.com/oppenheimer-1080p.mp4', 'type' => 'mp4', 'quality' => '1080p', 'priority' => 1],
                    ['label' => 'Server 2 - 720p', 'url' => 'https://example.com/oppenheimer-720p.mp4', 'type' => 'mp4', 'quality' => '720p', 'priority' => 2],
                ],
            ],
        ];
    }

    public function run(): void
    {
        $movies = [];

        foreach ($this->moviesData() as $key => $data) {
            $sources = $data['sources'];
            unset($data['sources']);

            $movie = Movie::create($data);

            foreach ($sources as $source) {
                MovieVideoSource::create([
                    'movie_id' => $movie->id,
                    'label' => $source['label'],
                    'url' => $source['url'],
                    'type' => $source['type'],
                    'quality' => $source['quality'],
                    'priority' => $source['priority'],
                    'is_active' => true,
                ]);
            }

            $movies[$key] = $movie;
        }

        $monday = Carbon::now()->startOfWeek(Carbon::MONDAY);

        WeeklyPick::create([
            'week_start_date' => $monday->format('Y-m-d'),
            'thursday_movie_id' => $movies[9]->id,
            'friday_movie_id' => $movies[10]->id,
            'published_at' => now(),
        ]);

        for ($i = 1; $i <= 4; $i++) {
            $weekStart = $monday->copy()->subWeeks($i);
            $thursdayIndex = ($i * 2) - 1;
            $fridayIndex = ($i * 2);

            WeeklyPick::create([
                'week_start_date' => $weekStart->format('Y-m-d'),
                'thursday_movie_id' => $movies[$thursdayIndex]->id,
                'friday_movie_id' => $movies[$fridayIndex]->id,
                'published_at' => $weekStart->endOfWeek(),
            ]);
        }
    }
}
