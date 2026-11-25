@php
    $supported = config('portfolio.locales.supported');
    $locale = request()->segment(1);

    if (! in_array($locale, $supported, true)) {
        $locale = config('portfolio.locales.default');
    }
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ trans('site.errors.404.meta_title', [], $locale) }} — {{ trans('site.brand', [], $locale) }}</title>
    <meta name="description" content="{{ trans('site.errors.404.description', [], $locale) }}">
    <meta name="robots" content="noindex">
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-background font-sans text-foreground antialiased">
    <main class="mx-auto flex min-h-screen w-full max-w-6xl items-center px-4 py-20 sm:px-6 lg:px-8">
        <div class="relative w-full overflow-hidden rounded-3xl border border-white/10 bg-surface p-8 text-center sm:p-12">
            <div class="pointer-events-none absolute -right-20 -top-20 h-56 w-56 rounded-full bg-accent/20 blur-3xl"></div>
            <div class="relative">
                <p class="mx-auto mb-6 inline-flex rounded-full border border-accent/20 bg-accent/10 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-accent">404</p>
                <h1 class="text-3xl font-semibold tracking-tight text-foreground sm:text-4xl">{{ trans('site.errors.404.title', [], $locale) }}</h1>
                <p class="mx-auto mt-4 max-w-md text-sm leading-7 text-muted">{{ trans('site.errors.404.description', [], $locale) }}</p>

                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <a href="{{ url('/'.$locale) }}" class="btn btn-primary">{{ trans('site.errors.404.home', [], $locale) }}</a>
                    <a href="{{ url('/'.$locale.'/projects') }}" class="btn btn-secondary">{{ trans('site.errors.404.projects', [], $locale) }}</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
