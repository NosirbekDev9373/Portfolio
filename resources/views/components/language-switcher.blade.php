@php
    $currentLocale = app()->getLocale();
    $currentRoute = request()->route();
    $routeName = $currentRoute?->getName() ?? 'home';
    $parameters = $currentRoute?->parameters() ?? [];
@endphp

<nav {{ $attributes->merge(['class' => 'flex items-center gap-1 rounded-full border border-white/10 p-1']) }} aria-label="{{ __('site.language.label') }}">
    @foreach (config('portfolio.locales.supported') as $locale)
        <a
            href="{{ route($routeName, array_merge($parameters, ['locale' => $locale])) }}"
            hreflang="{{ $locale }}"
            @if ($locale === $currentLocale) aria-current="true" @endif
            class="rounded-full px-2.5 py-1 text-xs font-semibold uppercase tracking-wide transition {{ $locale === $currentLocale ? 'bg-accent text-background' : 'text-muted hover:text-foreground' }}"
        >
            {{ $locale }}
        </a>
    @endforeach
</nav>
