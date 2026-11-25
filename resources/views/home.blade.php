@extends('layouts.public')

@section('content')
    <div class="mx-auto w-full max-w-6xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
        <div class="space-y-16 sm:space-y-20">
            <section data-reveal class="grid gap-4 lg:grid-cols-6">
                <div class="section-card relative overflow-hidden lg:col-span-4">
                    <div class="pointer-events-none absolute -right-24 -top-24 h-64 w-64 rounded-full bg-accent/20 blur-3xl"></div>
                    <div class="relative">
                        <x-section-label number="01" :label="__('site.sections.hero')" />
                        <p class="mt-6 text-sm uppercase tracking-[0.25em] text-muted">{{ __('site.hero.eyebrow') }}</p>
                        <h1 class="mt-3 text-4xl font-semibold tracking-tight text-foreground sm:text-5xl">
                            {{ config('portfolio.owner.name') }}
                        </h1>
                        <p class="mt-4 max-w-2xl text-lg text-foreground/90">{{ __('site.hero.heading') }}</p>
                        <p class="mt-4 max-w-2xl text-sm leading-7 text-muted">{{ __('site.hero.description') }}</p>

                        <div class="mt-8 flex flex-wrap gap-3">
                            <a href="{{ route('projects.index') }}" class="btn btn-primary">
                                {{ __('site.hero.view_projects') }}
                                <span aria-hidden="true">→</span>
                            </a>
                            <a href="{{ config('portfolio.owner.github') }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">
                                {{ __('site.hero.github') }}
                            </a>
                            <a href="{{ config('portfolio.owner.linkedin') }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">
                                {{ __('site.hero.linkedin') }}
                            </a>
                            <a href="mailto:{{ config('portfolio.owner.email') }}" class="btn btn-secondary">
                                {{ __('site.hero.email') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 lg:col-span-2">
                    <div class="section-card flex flex-col justify-between">
                        <p class="section-label">{{ __('site.hero.production_label') }}</p>
                        <p class="mt-6 text-5xl font-semibold tracking-tight text-foreground">{{ $productionProjectsCount }}</p>
                    </div>
                    <div class="section-card">
                        <p class="section-label">{{ __('site.hero.core_stack') }}</p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach (['PHP', 'Laravel', 'PostgreSQL'] as $technology)
                                <span class="chip">{{ $technology }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <section id="about" data-reveal class="scroll-mt-24 grid gap-4 lg:grid-cols-3">
                <div class="section-card lg:col-span-2">
                    <x-section-label number="02" :label="__('site.sections.about')" />
                    <h2 class="section-heading">{{ __('site.about.heading') }}</h2>
                    <p class="mt-4 max-w-3xl text-sm leading-7 text-muted">{{ __('site.about.placeholder') }}</p>
                </div>
                <div class="section-card">
                    <p class="section-label">{{ __('site.about.focus') }}</p>
                    <p class="mt-4 text-sm leading-7 text-muted">{{ __('site.about.focus_placeholder') }}</p>
                </div>
            </section>

            <section id="projects" data-reveal class="scroll-mt-24">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <x-section-label number="03" :label="__('site.sections.projects')" />
                        <h2 class="section-heading">{{ __('site.projects.heading') }}</h2>
                    </div>
                    <div class="flex flex-col gap-3 sm:items-end">
                        <p class="max-w-md text-sm leading-7 text-muted">{{ __('site.projects.note') }}</p>
                        <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                            {{ __('site.projects.view_all') }}
                            <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 lg:grid-cols-2">
                    @forelse ($featuredProjects as $project)
                        @php
                            $statusClasses = $project->status === 'production'
                                ? 'border-emerald-400/30 bg-emerald-400/10 text-emerald-300'
                                : 'border-amber-400/30 bg-amber-400/10 text-amber-300';
                        @endphp

                        <article class="flex flex-col overflow-hidden rounded-3xl border border-white/10 bg-surface transition duration-300 hover:-translate-y-1 hover:border-accent/40">
                            <div class="relative aspect-[16/10] overflow-hidden border-b border-white/10 bg-linear-to-br from-accent/25 via-surface to-background">
                                <span class="absolute -bottom-6 right-4 text-8xl font-semibold text-white/5" aria-hidden="true">
                                    {{ \Illuminate\Support\Str::substr($project->localized('title') ?? '', 0, 1) }}
                                </span>
                                <div class="absolute inset-0 flex items-center justify-center px-6 text-center">
                                    <span class="text-xs uppercase tracking-[0.3em] text-muted">{{ __('site.projects.screenshot_placeholder') }}</span>
                                </div>
                            </div>

                            <div class="flex flex-1 flex-col gap-5 p-6 sm:p-7">
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="rounded-full border px-3 py-1 text-xs font-medium uppercase tracking-wide {{ $statusClasses }}">
                                        {{ __('site.projects.status.'.$project->status) }}
                                    </span>
                                </div>

                                <h3 class="text-2xl font-semibold text-foreground">{{ $project->localized('title') }}</h3>
                                <p class="text-sm leading-7 text-muted">{{ $project->localized('summary') }}</p>

                                <div class="flex flex-wrap gap-2">
                                    @foreach ($project->technologies as $technology)
                                        <span class="chip">{{ $technology->name }}</span>
                                    @endforeach
                                </div>

                                <div class="mt-auto flex flex-wrap gap-3 pt-2">
                                    <a href="{{ route('projects.show', ['project' => $project]) }}" class="btn btn-primary">
                                        {{ __('site.projects.case_study') }}
                                        <span aria-hidden="true">→</span>
                                    </a>

                                    @if ($project->live_url)
                                        <a href="{{ $project->live_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">
                                            {{ __('site.projects.live_demo') }}
                                            <span aria-hidden="true">↗</span>
                                        </a>
                                    @endif

                                    @if ($project->github_url)
                                        <a href="{{ $project->github_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">
                                            {{ __('site.projects.github') }}
                                            <span aria-hidden="true">↗</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <p class="section-card text-sm text-muted lg:col-span-2">{{ __('site.projects.empty') }}</p>
                    @endforelse
                </div>
            </section>

            <section id="skills" data-reveal class="scroll-mt-24">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <x-section-label number="04" :label="__('site.sections.skills')" />
                        <h2 class="section-heading">{{ __('site.skills.heading') }}</h2>
                    </div>
                    <p class="max-w-md text-sm leading-7 text-muted">{{ __('site.skills.note') }}</p>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach (config('portfolio.skill_groups') as $group => $labelKey)
                        <div class="section-card">
                            <p class="section-label">{{ __($labelKey) }}</p>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @forelse ($technologiesByGroup->get($group, collect()) as $technology)
                                    <span class="chip">{{ $technology->name }}</span>
                                @empty
                                    <span class="text-sm text-muted">—</span>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section id="experience" data-reveal class="scroll-mt-24 grid gap-4 lg:grid-cols-3">
                <div class="section-card">
                    <x-section-label number="05" :label="__('site.sections.journey')" />
                    <h2 class="section-heading">{{ __('site.journey.heading') }}</h2>
                    <p class="mt-4 text-sm leading-7 text-muted">{{ __('site.journey.note') }}</p>
                </div>

                <div class="section-card lg:col-span-2">
                    <ol class="space-y-6">
                        @foreach (__('site.journey.items') as $item)
                            <li class="relative border-l border-white/10 pl-6">
                                <span class="absolute -left-1.5 top-1.5 h-3 w-3 rounded-full border border-accent/40 bg-accent/20"></span>
                                <p class="text-sm font-semibold text-foreground">{{ $item['title'] }}</p>
                                <p class="mt-1 text-sm leading-7 text-muted">{{ $item['description'] }}</p>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </section>

            <section id="github" data-reveal class="scroll-mt-24 grid gap-4 lg:grid-cols-3">
                <div class="section-card">
                    <x-section-label number="06" :label="__('site.sections.github')" />
                    <h2 class="section-heading">{{ __('site.github.heading') }}</h2>
                    <p class="mt-4 text-sm leading-7 text-muted">{{ __('site.github.placeholder') }}</p>
                    <a href="{{ config('portfolio.owner.github') }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary mt-6">
                        {{ __('site.github.profile') }}
                        <span aria-hidden="true">↗</span>
                    </a>
                </div>

                <div class="section-card lg:col-span-2">
                    <p class="section-label">{{ __('site.github.repositories') }}</p>
                    <div class="mt-4 space-y-3">
                        @forelse ($repositories as $repository)
                            <a href="{{ $repository->github_url }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between gap-4 rounded-2xl border border-white/10 px-4 py-3 transition hover:border-accent/40 hover:bg-white/5">
                                <span class="text-sm font-medium text-foreground">{{ $repository->localized('title') }}</span>
                                <span class="text-xs text-muted">{{ __('site.github.view_repository') }} ↗</span>
                            </a>
                        @empty
                            <p class="text-sm text-muted">{{ __('site.github.empty') }}</p>
                        @endforelse
                    </div>
                </div>
            </section>

            <section id="contact" data-reveal class="scroll-mt-24">
                <div class="section-card relative overflow-hidden">
                    <div class="pointer-events-none absolute -right-20 -top-20 h-56 w-56 rounded-full bg-accent/20 blur-3xl"></div>
                    <div class="relative">
                        <x-section-label number="07" :label="__('site.sections.contact')" />
                        <div class="mt-6 grid gap-8 lg:grid-cols-2 lg:items-end">
                            <div>
                                <h2 class="text-3xl font-semibold tracking-tight text-foreground sm:text-4xl">{{ __('site.contact.heading') }}</h2>
                                <p class="mt-4 max-w-xl text-sm leading-7 text-muted">{{ __('site.contact.placeholder') }}</p>
                            </div>
                            <div class="flex flex-wrap gap-3 lg:justify-end">
                                <a href="mailto:{{ config('portfolio.owner.email') }}" class="btn btn-primary">{{ __('site.contact.email') }}</a>
                                <a href="{{ config('portfolio.owner.linkedin') }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">{{ __('site.contact.linkedin') }}</a>
                                <a href="{{ config('portfolio.owner.github') }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">{{ __('site.contact.github') }}</a>
                            </div>
                        </div>
                        <div class="mt-8 flex flex-wrap gap-x-6 gap-y-2 text-sm text-muted">
                            <a href="mailto:{{ config('portfolio.owner.email') }}" class="transition hover:text-foreground">{{ config('portfolio.owner.email') }}</a>
                            <a href="{{ config('portfolio.owner.linkedin') }}" target="_blank" rel="noopener noreferrer" class="transition hover:text-foreground">LinkedIn</a>
                            <a href="{{ config('portfolio.owner.github') }}" target="_blank" rel="noopener noreferrer" class="transition hover:text-foreground">GitHub</a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
