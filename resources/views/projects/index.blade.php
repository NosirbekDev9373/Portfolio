@extends('layouts.public')

@section('title', __('site.projects.index_title'))
@section('meta_description', __('site.projects.index_description'))

@section('content')
    <div class="mx-auto w-full max-w-6xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
        <section data-reveal class="section-card relative overflow-hidden">
            <div class="pointer-events-none absolute -right-24 -top-24 h-64 w-64 rounded-full bg-accent/20 blur-3xl"></div>
            <div class="relative">
                <p class="section-label">{{ __('site.nav.projects') }}</p>
                <h1 class="section-heading text-3xl sm:text-4xl">{{ __('site.projects.index_heading') }}</h1>
                <p class="mt-4 max-w-2xl text-sm leading-7 text-muted">{{ __('site.projects.index_note') }}</p>
            </div>
        </section>

        <section class="mt-8">
            <div class="grid gap-4 lg:grid-cols-2">
                @forelse ($projects as $project)
                    @php
                        $statusClasses = $project->status === 'production'
                            ? 'border-emerald-400/30 bg-emerald-400/10 text-emerald-300'
                            : 'border-amber-400/30 bg-amber-400/10 text-amber-300';
                    @endphp

                    <article data-reveal class="flex flex-col overflow-hidden rounded-3xl border border-white/10 bg-surface transition duration-300 hover:-translate-y-1 hover:border-accent/40">
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

                            <h2 class="text-2xl font-semibold text-foreground">
                                <a href="{{ route('projects.show', ['project' => $project]) }}" class="transition hover:text-accent">
                                    {{ $project->localized('title') }}
                                </a>
                            </h2>
                            <p class="text-sm leading-7 text-muted">{{ $project->localized('summary') }}</p>

                            <div class="flex flex-wrap gap-2">
                                @foreach ($project->technologies as $technology)
                                    <span class="chip">{{ $technology->name }}</span>
                                @endforeach
                            </div>

                            <div class="mt-auto flex flex-wrap items-center gap-3 pt-2">
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
    </div>
@endsection
