<div class="space-y-2">
    <p class="text-sm text-gray-500 dark:text-gray-400">Select a movie to fetch details:</p>
    <ul class="divide-y divide-gray-200 dark:divide-gray-700 rounded-lg border border-gray-200 dark:border-gray-700 max-h-80 overflow-y-auto">
        @foreach($movies as $movie)
            @php
                $year = isset($movie['release_date']) && $movie['release_date'] ? substr($movie['release_date'], 0, 4) : '?';
                $label = ($movie['title'] ?? 'Unknown') . ' (' . $year . ')';
            @endphp
            <li>
                <button
                    type="button"
                    wire:click="selectTmdbMovie({{ (int) $movie['id'] }})"
                    class="w-full px-4 py-3 text-left text-sm font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800 focus:bg-gray-50 dark:focus:bg-gray-800 focus:outline-none transition"
                >
                    {{ $label }}
                </button>
            </li>
        @endforeach
    </ul>
</div>
