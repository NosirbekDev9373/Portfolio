@props([
    'images' => [],
    'fallbackTitle' => '',
    'compact' => false,
])

@php
    $disk = \Illuminate\Support\Facades\Storage::disk('public');
    $items = is_iterable($images)
        ? collect($images)
            ->filter(fn ($image): bool => is_object($image) && isset($image->path) && $disk->exists($image->path))
            ->values()
        : collect();
    $hasImages = $items->isNotEmpty();
    $showControls = ! $compact && $hasImages && $items->count() > 1;
    $rootClass = $compact ? 'project-carousel' : 'project-carousel mt-6';
    $trackClass = $compact
        ? 'flex touch-pan-x snap-x snap-mandatory overflow-x-auto'
        : 'flex touch-pan-x snap-x snap-mandatory gap-4 overflow-x-auto pb-3 [scrollbar-width:thin]';
    $slideClass = $compact
        ? 'w-full shrink-0 snap-start'
        : 'w-[90%] shrink-0 snap-start sm:w-[70%] lg:w-[60%]';
    $figureClass = $compact
        ? 'overflow-hidden bg-transparent'
        : 'overflow-hidden rounded-3xl border border-white/10 bg-surface';
@endphp

<div
    {{ $attributes->merge(['class' => $rootClass]) }}
    data-project-carousel
    role="region"
    aria-roledescription="carousel"
    aria-label="{{ __('site.case_study.screenshots') }}"
    @if ($showControls)
        data-carousel-autoplay
    @endif
>
    <div
        data-carousel-track
        class="{{ $trackClass }}"
        tabindex="0"
    >
        @forelse ($items as $image)
            @php
                $screenshotSize = @getimagesize($disk->path($image->path));
                $width = $screenshotSize[0] ?? 1600;
                $height = $screenshotSize[1] ?? 1000;
                $alt = $image->localizedAlt() ?: $fallbackTitle;
            @endphp

            <figure data-carousel-slide class="{{ $slideClass }} {{ $figureClass }}">
                <img
                    src="{{ asset('storage/'.$image->path) }}"
                    width="{{ $width }}"
                    height="{{ $height }}"
                    alt="{{ $alt }}"
                    class="aspect-[16/10] w-full object-cover"
                    loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                >
                @unless ($compact)
                    <figcaption class="px-4 py-3 text-xs text-muted">{{ $alt }}</figcaption>
                @endunless
            </figure>
        @empty
            @if ($compact)
                <div data-carousel-slide class="{{ $slideClass }} relative flex aspect-[16/10] items-center justify-center overflow-hidden border-b border-white/10 bg-linear-to-br from-accent/25 via-surface to-background">
                    <span class="absolute -bottom-6 right-4 text-8xl font-semibold text-white/5" aria-hidden="true">
                        {{ \Illuminate\Support\Str::substr($fallbackTitle ?? '', 0, 1) }}
                    </span>
                    <span class="relative px-6 text-center text-xs uppercase tracking-[0.3em] text-muted">{{ __('site.projects.screenshot_placeholder') }}</span>
                </div>
            @else
                @for ($i = 0; $i < 2; $i++)
                    <div data-carousel-slide class="{{ $slideClass }} flex aspect-[16/10] items-center justify-center rounded-3xl border border-white/10 bg-linear-to-br from-accent/20 via-surface to-background">
                        <span class="text-xs uppercase tracking-[0.3em] text-muted">{{ __('site.case_study.screenshot_placeholder') }}</span>
                    </div>
                @endfor
            @endif
        @endforelse
    </div>

    @if ($showControls)
        <div class="mt-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    data-carousel-prev
                    class="btn btn-secondary disabled:cursor-not-allowed disabled:opacity-40"
                    aria-label="{{ __('site.case_study.carousel_previous') }}"
                >
                    <span aria-hidden="true">←</span>
                </button>
                <button
                    type="button"
                    data-carousel-next
                    class="btn btn-secondary disabled:cursor-not-allowed disabled:opacity-40"
                    aria-label="{{ __('site.case_study.carousel_next') }}"
                >
                    <span aria-hidden="true">→</span>
                </button>
            </div>

            <div data-carousel-dots class="flex items-center gap-2" aria-label="{{ __('site.case_study.carousel_dots') }}">
                @foreach ($items as $image)
                    <button
                        type="button"
                        data-carousel-dot
                        data-carousel-dot-index="{{ $loop->index }}"
                        @class([
                            'size-2 rounded-full transition',
                            'bg-accent' => $loop->first,
                            'bg-white/25 hover:bg-white/50' => ! $loop->first,
                        ])
                        aria-label="{{ __('site.case_study.carousel_go_to') }} {{ $loop->iteration }}"
                        @if ($loop->first) aria-current="true" @endif
                    ></button>
                @endforeach
            </div>

            <p data-carousel-status class="text-xs text-muted" aria-live="polite">1 / {{ $items->count() }}</p>
        </div>
    @endif
</div>
