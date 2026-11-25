<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>document.documentElement.classList.add('js');</script>
    <title>@yield('title', __('site.meta.title'))</title>
    <meta name="description" content="@yield('meta_description', __('site.meta.description'))">
    <link rel="canonical" href="{{ url()->current() }}">
    @include('partials.hreflang')

    @php
        $fontFiles = glob(public_path('build/assets/inter-400-normal-*.woff2')) ?: [];
        $fontFile = $fontFiles[0] ?? null;
    @endphp
    @if ($fontFile)
        <link rel="preload" href="{{ asset('build/assets/'.basename($fontFile)) }}" as="font" type="font/woff2" crossorigin>
    @endif

    @php
        $seoTitle = trim((string) $__env->yieldContent('title', __('site.meta.title')));
        $seoDescription = trim((string) $__env->yieldContent('meta_description', __('site.meta.description')));
        $seoType = trim((string) $__env->yieldContent('og_type', 'website'));
        $seoImage = trim((string) $__env->yieldContent('og_image', ''));
        $currentLocale = app()->getLocale();
        $ogLocale = config('portfolio.locales.og.'.$currentLocale, 'en_US');
    @endphp

    <meta property="og:type" content="{{ $seoType }}">
    <meta property="og:site_name" content="{{ __('site.brand') }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="{{ $ogLocale }}">
    @foreach (config('portfolio.locales.supported') as $locale)
        @continue($locale === $currentLocale)
        <meta property="og:locale:alternate" content="{{ config('portfolio.locales.og.'.$locale) }}">
    @endforeach
    @if ($seoImage !== '')
        <meta property="og:image" content="{{ $seoImage }}">
    @endif

    <meta name="twitter:card" content="{{ $seoImage !== '' ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    @if ($seoImage !== '')
        <meta name="twitter:image" content="{{ $seoImage }}">
    @endif

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-background font-sans text-foreground antialiased">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-full focus:bg-accent focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-background">
        {{ __('site.skip_to_content') }}
    </a>

    @include('partials.navbar')

    <main id="main-content">
        @yield('content')
    </main>

    @include('partials.footer')
</body>
</html>
