<x-filament-widgets::widget class="fi-current-week-pick">
    @php $pick = $this->getWeeklyPick(); @endphp

    <x-filament::section
        :heading="__('أفلام الأسبوع')"
    >
        <x-slot name="afterHeader">
            @if($pick && $pick->published_at)
                <x-filament::badge color="success">{{ __('منشور') }}</x-filament::badge>
            @elseif($pick)
                <x-filament::badge color="warning">{{ __('مسودة') }}</x-filament::badge>
            @endif
        </x-slot>

        @if($pick)
            <div
                class="fi-current-week-row"
                style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;"
            >
                @foreach(['thursday' => ['movie' => $pick->thursdayMovie, 'day' => __('الخميس')], 'friday' => ['movie' => $pick->fridayMovie, 'day' => __('الجمعة')]] as $key => $dayData)
                    <div
                        class="fi-current-week-card"
                        style="display: flex; align-items: center; gap: 0.75rem; border-radius: 0.5rem; border: 1px solid rgba(0,0,0,0.08); background: rgba(0,0,0,0.02); padding: 0.5rem 0.75rem;"
                    >
                        <div
                            class="fi-current-week-poster-wrap"
                            style="flex-shrink: 0; overflow: hidden; border-radius: 0.25rem; background: rgba(0,0,0,0.06);"
                        >
                            @if($dayData['movie']?->poster_url)
                                <img
                                    src="{{ $dayData['movie']->poster_url }}"
                                    alt="{{ $dayData['movie']->title }}"
                                    class="h-full w-full object-cover"
                                    style="height: 120px"
                                >
                            @else
                                <div style="display: flex; height: 100%; width: 100%; align-items: center; justify-content: center;">
                                    <svg style="width: 1.25rem; height: 1.25rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="fi-current-week-body" style="min-width: 0; flex: 1;">
                            <p class="fi-current-week-day text-[10px] font-semibold uppercase tracking-wider text-primary-600 dark:text-primary-400">
                                {{ $dayData['day'] }}
                            </p>
                            <p class="fi-current-week-title truncate text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $dayData['movie']?->title ?? __('غير محدد') }}
                            </p>
                            @if($dayData['movie'])
                                <div class="fi-current-week-actions mt-1 flex gap-2">
                                    <x-filament::link
                                        :href="route('filament.admin.resources.movies.edit', $dayData['movie'])"
                                        size="xs"
                                        color="primary"
                                    >
                                        {{ __('تعديل') }}
                                    </x-filament::link>
                                    <x-filament::link
                                        :href="route('watch', $dayData['movie'])"
                                        size="xs"
                                        color="gray"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        {{ __('معاينة') }}
                                    </x-filament::link>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="fi-current-week-empty flex flex-col items-center justify-center py-8 text-center">
                <svg class="fi-current-week-empty-icon h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="fi-current-week-empty-text mt-2 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('لم يتم تحديد أفلام الأسبوع بعد') }}
                </p>
                <x-filament::link
                    :href="route('filament.admin.resources.movies.index')"
                    class="mt-3"
                >
                    {{ __('إضافة أفلام') }}
                </x-filament::link>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
