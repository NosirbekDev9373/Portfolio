<header class="sticky top-0 z-50 border-b border-white/10 bg-background/80 backdrop-blur">
    <div class="mx-auto flex w-full max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="text-sm font-semibold uppercase tracking-[0.2em] text-foreground">
            {{ __('site.brand') }}
        </a>

        <nav class="hidden items-center gap-1 lg:flex" aria-label="{{ __('site.nav.primary') }}">
            <x-nav-links variant="desktop" />
        </nav>

        <div class="flex items-center gap-3">
            <x-language-switcher />

            <button
                type="button"
                data-nav-toggle
                aria-expanded="false"
                aria-controls="mobile-menu"
                class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/10 text-foreground transition hover:bg-white/5 lg:hidden"
            >
                <span class="sr-only">{{ __('site.nav.toggle') }}</span>
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
                </svg>
            </button>
        </div>
    </div>

    <div id="mobile-menu" data-nav-menu class="hidden border-t border-white/10 lg:hidden">
        <nav class="mx-auto flex w-full max-w-6xl flex-col gap-1 px-4 py-4 sm:px-6" aria-label="{{ __('site.nav.mobile') }}">
            <x-nav-links variant="mobile" />
        </nav>
    </div>
</header>
