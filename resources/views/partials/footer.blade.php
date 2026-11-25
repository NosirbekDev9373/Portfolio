<footer class="border-t border-white/10 bg-surface/40">
    <div class="mx-auto flex w-full max-w-6xl flex-col gap-8 px-4 py-10 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-foreground">{{ __('site.brand') }}</p>
            <p class="mt-2 text-sm text-muted">{{ __('site.footer.tagline') }}</p>
        </div>

        <nav class="flex flex-wrap items-center gap-x-5 gap-y-2" aria-label="{{ __('site.nav.footer') }}">
            <x-nav-links variant="footer" />
        </nav>

        <div class="flex flex-col gap-3 lg:items-end">
            <div class="flex flex-wrap gap-x-5 gap-y-2 text-sm text-muted">
                <a href="mailto:{{ config('portfolio.owner.email') }}" class="transition hover:text-foreground">{{ __('site.contact.email') }}</a>
                <a href="{{ config('portfolio.owner.linkedin') }}" target="_blank" rel="noopener noreferrer" class="transition hover:text-foreground">{{ __('site.contact.linkedin') }}</a>
                <a href="{{ config('portfolio.owner.github') }}" target="_blank" rel="noopener noreferrer" class="transition hover:text-foreground">{{ __('site.contact.github') }}</a>
            </div>
            <p class="text-sm text-muted">© {{ date('Y') }} {{ __('site.brand') }}. {{ __('site.footer.rights') }}</p>
        </div>
    </div>
</footer>
