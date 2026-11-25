@extends('layouts.public')

@php
    $title = $project->localized('title');
    $summary = $project->localized('summary') ?? $project->localized('description') ?? __('site.meta.description');
    $overview = $project->localized('description') ?? $project->localized('summary') ?? __('site.case_study.overview_placeholder');
    $problem = $project->localizedProblem();
    $solution = $project->localizedSolution();
    $features = $project->localizedFeatures();
    $contributions = $project->localizedContributions();
    $metaDescription = \Illuminate\Support\Str::limit(strip_tags($summary), 155);
@endphp

@section('title', $title.' — '.__('site.case_study.title_suffix'))
@section('meta_description', $metaDescription)
@section('og_type', 'article')

@if ($ogImage)
    @section('og_image', $ogImage)
@endif

@section('content')
    <div class="mx-auto w-full max-w-6xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
        <nav class="flex flex-wrap items-center gap-2 text-xs text-muted" aria-label="{{ __('site.case_study.breadcrumb') }}">
            <a href="{{ route('home') }}" class="transition hover:text-foreground">{{ __('site.nav.home') }}</a>
            <span aria-hidden="true">/</span>
            <a href="{{ route('projects.index') }}" class="transition hover:text-foreground">{{ __('site.nav.projects') }}</a>
            <span aria-hidden="true">/</span>
            <span class="text-foreground" aria-current="page">{{ $title }}</span>
        </nav>

        <section data-reveal class="mt-6 grid gap-4 lg:grid-cols-3">
            @php
                $statusClasses = $project->status === 'production'
                    ? 'border-emerald-400/30 bg-emerald-400/10 text-emerald-300'
                    : 'border-amber-400/30 bg-amber-400/10 text-amber-300';
            @endphp

            <div class="section-card relative overflow-hidden lg:col-span-2">
                <div class="pointer-events-none absolute -right-24 -top-24 h-64 w-64 rounded-full bg-accent/20 blur-3xl"></div>
                <div class="relative">
                    <p class="section-label">{{ __('site.case_study.title_suffix') }}</p>

                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        <span class="rounded-full border px-3 py-1 text-xs font-medium uppercase tracking-wide {{ $statusClasses }}">
                            {{ __('site.projects.status.'.$project->status) }}
                        </span>
                    </div>

                    <h1 class="mt-4 text-3xl font-semibold tracking-tight text-foreground sm:text-4xl">{{ $title }}</h1>
                    <p class="mt-4 max-w-3xl text-sm leading-7 text-muted">{{ $summary }}</p>

                    <div class="mt-6 flex flex-wrap gap-2">
                        @foreach ($project->technologies as $technology)
                            <span class="chip">{{ $technology->name }}</span>
                        @endforeach
                    </div>

                    <div class="mt-8 flex flex-wrap gap-3">
                        @if ($project->live_url)
                            <a href="{{ $project->live_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                                {{ __('site.projects.live_demo') }}
                                <span aria-hidden="true">↗</span>
                            </a>
                        @endif

                        @if ($project->github_url)
                            <a href="{{ $project->github_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">
                                {{ __('site.case_study.repository') }}
                                <span aria-hidden="true">↗</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="section-card flex flex-col justify-between">
                <div>
                    <p class="section-label">{{ __('site.case_study.quick_links') }}</p>
                    <div class="mt-4 space-y-3 text-sm">
                        @if ($project->live_url)
                            <a href="{{ $project->live_url }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between gap-3 rounded-2xl border border-white/10 px-4 py-3 transition hover:border-accent/40 hover:bg-white/5">
                                <span>{{ __('site.projects.live_demo') }}</span>
                                <span aria-hidden="true" class="text-muted">↗</span>
                            </a>
                        @endif

                        @if ($project->github_url)
                            <a href="{{ $project->github_url }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between gap-3 rounded-2xl border border-white/10 px-4 py-3 transition hover:border-accent/40 hover:bg-white/5">
                                <span>{{ __('site.case_study.repository') }}</span>
                                <span aria-hidden="true" class="text-muted">↗</span>
                            </a>
                        @else
                            <p class="text-sm text-muted">{{ __('site.case_study.repository_private') }}</p>
                        @endif
                    </div>
                </div>

                <a href="{{ route('projects.index') }}" class="btn btn-secondary mt-6 self-start">
                    <span aria-hidden="true">←</span>
                    {{ __('site.case_study.back') }}
                </a>
            </div>
        </section>

        <div class="mt-12 space-y-8">
            <section id="overview" data-reveal class="section-card scroll-mt-24">
                <x-section-label number="01" :label="__('site.case_study.overview')" />
                <h2 class="section-heading">{{ __('site.case_study.overview') }}</h2>
                <p class="mt-4 max-w-4xl text-sm leading-7 text-muted">{!! nl2br(e($overview)) !!}</p>
            </section>

            @if ($problem !== null || $solution !== null)
                <section id="problem-solution" data-reveal class="scroll-mt-24">
                    <x-section-label number="02" :label="__('site.case_study.problem_solution')" />
                    <h2 class="section-heading">{{ __('site.case_study.problem_solution') }}</h2>

                    <div class="mt-6 grid gap-4 lg:grid-cols-2">
                        @if ($problem !== null)
                            <div class="section-card">
                                <p class="section-label">{{ __('site.case_study.problem') }}</p>
                                <p class="mt-4 text-sm leading-7 text-muted">{!! nl2br(e($problem)) !!}</p>
                            </div>
                        @endif
                        @if ($solution !== null)
                            <div class="section-card">
                                <p class="section-label">{{ __('site.case_study.solution') }}</p>
                                <p class="mt-4 text-sm leading-7 text-muted">{!! nl2br(e($solution)) !!}</p>
                            </div>
                        @endif
                    </div>
                </section>
            @endif

            @if (! empty($features))
                <section id="key-features" data-reveal class="scroll-mt-24">
                    <x-section-label number="03" :label="__('site.case_study.key_features')" />
                    <h2 class="section-heading">{{ __('site.case_study.key_features') }}</h2>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($features as $feature)
                            <div class="section-card">
                                @if ($feature['title'] !== null)
                                    <p class="text-sm font-semibold text-foreground">{!! nl2br(e($feature['title'])) !!}</p>
                                @endif
                                @if ($feature['description'] !== null)
                                    <p class="mt-2 text-sm leading-7 text-muted">{!! nl2br(e($feature['description'])) !!}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <section id="technology-stack" data-reveal class="scroll-mt-24">
                <x-section-label number="04" :label="__('site.case_study.technology_stack')" />
                <h2 class="section-heading">{{ __('site.case_study.technology_stack') }}</h2>

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

            <section id="screenshots" data-reveal class="scroll-mt-24">
                <x-section-label number="05" :label="__('site.case_study.screenshots')" />
                <h2 class="section-heading">{{ __('site.case_study.screenshots') }}</h2>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    @forelse ($screenshots as $image)
                        @php
                            $screenshotSize = @getimagesize(\Illuminate\Support\Facades\Storage::disk('public')->path($image->path));
                        @endphp
                        <figure class="overflow-hidden rounded-3xl border border-white/10 bg-surface">
                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($image->path) }}"
                                @if ($screenshotSize) width="{{ $screenshotSize[0] }}" height="{{ $screenshotSize[1] }}" @endif
                                alt="{{ $image->localizedAlt() ?? $title }}"
                                class="aspect-[16/10] w-full object-cover"
                                loading="lazy"
                            >
                            <figcaption class="px-4 py-3 text-xs text-muted">{{ $image->localizedAlt() ?? $title }}</figcaption>
                        </figure>
                    @empty
                        @for ($i = 0; $i < 2; $i++)
                            <div class="flex aspect-[16/10] items-center justify-center rounded-3xl border border-white/10 bg-linear-to-br from-accent/20 via-surface to-background">
                                <span class="text-xs uppercase tracking-[0.3em] text-muted">{{ __('site.case_study.screenshot_placeholder') }}</span>
                            </div>
                        @endfor
                    @endforelse
                </div>
            </section>

            @if (! empty($contributions))
                <section id="contribution" data-reveal class="section-card scroll-mt-24">
                    <x-section-label number="06" :label="__('site.case_study.contribution')" />
                    <h2 class="section-heading">{{ __('site.case_study.contribution') }}</h2>

                    <ul class="mt-6 space-y-4">
                        @foreach ($contributions as $contribution)
                            <li class="flex gap-3 text-sm leading-7 text-muted">
                                <span aria-hidden="true" class="mt-0.5 text-accent">✓</span>
                                <div>
                                    @if ($contribution['title'] !== null)
                                        <p class="font-semibold text-foreground">{!! nl2br(e($contribution['title'])) !!}</p>
                                    @endif
                                    @if ($contribution['description'] !== null)
                                        <p class="mt-1">{!! nl2br(e($contribution['description'])) !!}</p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            <section id="links" data-reveal class="section-card scroll-mt-24">
                <x-section-label number="07" :label="__('site.case_study.live_demo_repository')" />
                <h2 class="section-heading">{{ __('site.case_study.live_demo_repository') }}</h2>

                <div class="mt-6 flex flex-wrap gap-3">
                    @if ($project->live_url)
                        <a href="{{ $project->live_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                            {{ __('site.projects.live_demo') }}
                            <span aria-hidden="true">↗</span>
                        </a>
                    @endif

                    @if ($project->github_url)
                        <a href="{{ $project->github_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">
                            {{ __('site.case_study.repository') }}
                            <span aria-hidden="true">↗</span>
                        </a>
                    @else
                        <p class="text-sm text-muted">{{ __('site.case_study.repository_private') }}</p>
                    @endif
                </div>

                <a href="{{ route('projects.index') }}" class="btn btn-secondary mt-8">
                    <span aria-hidden="true">←</span>
                    {{ __('site.case_study.back') }}
                </a>
            </section>
        </div>
    </div>
@endsection
