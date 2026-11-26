@extends('layouts.admin')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="mt-0 text-3xl font-semibold tracking-tight text-foreground">Skills</h1>
        <a href="{{ route('admin.skills.create') }}" class="btn btn-primary">New skill</a>
    </div>

    @foreach ($skillsByGroup as $group => $skills)
        <section class="section-card mt-8" data-skill-group="{{ $group }}">
            <h2 class="text-lg font-semibold text-foreground">{{ __('site.skills.groups.'.$group) }}</h2>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full border-collapse text-left text-sm">
                    <thead>
                        <tr class="border-b border-white/10 text-xs uppercase tracking-[0.2em] text-muted">
                            <th class="px-3 py-3 font-medium">Name</th>
                            <th class="px-3 py-3 font-medium">Status</th>
                            <th class="px-3 py-3 font-medium">Sort order</th>
                            <th class="px-3 py-3 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($skills as $skill)
                            <tr class="border-b border-white/10 last:border-0">
                                <td class="px-3 py-4 font-medium text-foreground">{{ $skill->name }}</td>
                                <td class="px-3 py-4">
                                    <span class="chip {{ $skill->status === 'learning' ? 'border-amber-400/30 text-amber-300' : 'border-emerald-400/30 text-emerald-300' }}">
                                        {{ $skill->status === 'learning' ? __('site.skills.learning') : 'Used' }}
                                    </span>
                                </td>
                                <td class="px-3 py-4 text-muted">{{ $skill->sort_order }}</td>
                                <td class="px-3 py-4">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a href="{{ route('admin.skills.edit', $skill) }}" class="btn btn-secondary">Edit</a>
                                        <form method="POST" action="{{ route('admin.skills.destroy', $skill) }}" onsubmit="return confirm('Delete this skill?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-secondary">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-6 text-sm text-muted">No skills in this group.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endforeach
@endsection
