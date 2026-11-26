@php
    $inputClass = 'mt-1 w-full rounded-xl border border-white/10 bg-background px-4 py-2 text-sm text-foreground placeholder:text-muted focus:border-accent focus:outline-none';
    $labelClass = 'block text-sm font-medium text-foreground';
    $errorClass = 'mt-1 text-sm text-red-400';
@endphp

<div class="grid gap-6 sm:grid-cols-2">
    <div>
        <label for="name" class="{{ $labelClass }}">Name</label>
        <input id="name" name="name" type="text" maxlength="80" value="{{ old('name', $skill->name) }}" class="{{ $inputClass }}" required>
        @error('name')
            <p class="{{ $errorClass }}">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="group" class="{{ $labelClass }}">Group</label>
        <select id="group" name="group" class="{{ $inputClass }}" required>
            @foreach ($groups as $group)
                <option value="{{ $group }}" @selected(old('group', $skill->group ?? 'backend') === $group)>{{ __('site.skills.groups.'.$group) }}</option>
            @endforeach
        </select>
        @error('group')
            <p class="{{ $errorClass }}">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="status" class="{{ $labelClass }}">Status</label>
        <select id="status" name="status" class="{{ $inputClass }}" required>
            @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(old('status', $skill->status) === $status)>{{ $status === 'learning' ? __('site.skills.learning') : 'Used' }}</option>
            @endforeach
        </select>
        @error('status')
            <p class="{{ $errorClass }}">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="sort_order" class="{{ $labelClass }}">Sort order</label>
        <input id="sort_order" name="sort_order" type="number" min="0" max="10000" value="{{ old('sort_order', $skill->sort_order ?? 0) }}" class="{{ $inputClass }}" required>
        @error('sort_order')
            <p class="{{ $errorClass }}">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="flex flex-wrap items-center gap-3">
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route('admin.skills.index') }}" class="btn btn-secondary">Cancel</a>
</div>
