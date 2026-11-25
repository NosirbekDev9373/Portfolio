@extends('layouts.admin')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="mt-0 text-3xl font-semibold tracking-tight text-foreground">Projects</h1>
        <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">New project</a>
    </div>

    <section class="section-card mt-8">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left text-sm">
                <thead>
                    <tr class="border-b border-white/10 text-xs uppercase tracking-[0.2em] text-muted">
                        <th class="px-3 py-3 font-medium">Title</th>
                        <th class="px-3 py-3 font-medium">Slug</th>
                        <th class="px-3 py-3 font-medium">Status</th>
                        <th class="px-3 py-3 font-medium">Badges</th>
                        <th class="px-3 py-3 font-medium">Sort order</th>
                        <th class="px-3 py-3 font-medium">Screenshots</th>
                        <th class="px-3 py-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($projects as $project)
                        <tr class="border-b border-white/10 last:border-0">
                            <td class="px-3 py-4 font-medium text-foreground">{{ $project->title_en }}</td>
                            <td class="px-3 py-4 font-mono text-xs text-muted">{{ $project->slug }}</td>
                            <td class="px-3 py-4">
                                <span class="chip {{ $project->status === 'production' ? 'border-emerald-400/30 text-emerald-300' : 'border-amber-400/30 text-amber-300' }}">
                                    {{ $project->status === 'production' ? 'Production' : 'In progress' }}
                                </span>
                            </td>
                            <td class="px-3 py-4">
                                <div class="flex flex-wrap gap-2">
                                    @if ($project->is_featured)
                                        <span class="chip border-accent/40 text-accent">Featured</span>
                                    @endif
                                    @if ($project->is_published)
                                        <span class="chip border-emerald-400/30 text-emerald-300">Published</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-4 text-muted">{{ $project->sort_order }}</td>
                            <td class="px-3 py-4 text-muted">{{ $project->images->count() }}</td>
                            <td class="px-3 py-4">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-secondary">Edit</a>
                                    <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" onsubmit="return confirm('Delete this project?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-secondary">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-6 text-sm text-muted">No projects yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
