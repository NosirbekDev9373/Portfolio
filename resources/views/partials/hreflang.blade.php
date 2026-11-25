@php
    $currentRoute = request()->route();
    $routeName = $currentRoute?->getName() ?? 'home';
    $parameters = $currentRoute?->parameters() ?? [];
    $defaultLocale = config('portfolio.locales.default');
@endphp

@foreach (config('portfolio.locales.supported') as $locale)
    <link rel="alternate" hreflang="{{ $locale }}" href="{{ route($routeName, array_merge($parameters, ['locale' => $locale])) }}">
@endforeach
<link rel="alternate" hreflang="x-default" href="{{ route($routeName, array_merge($parameters, ['locale' => $defaultLocale])) }}">
