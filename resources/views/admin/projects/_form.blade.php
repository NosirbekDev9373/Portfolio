@php
    $inputClass = 'mt-1 w-full rounded-xl border border-white/10 bg-background px-4 py-2 text-sm text-foreground placeholder:text-muted focus:border-accent focus:outline-none';
    $labelClass = 'block text-sm font-medium text-foreground';
    $errorClass = 'mt-1 text-sm text-red-400';
    $selectedTechnologies = array_map('intval', (array) old('technologies', $project->technologies->pluck('id')->all()));
    $selectedScreenshots = array_map('intval', (array) old('delete_screenshots', []));
    $featureRows = old('features', $project->features ?? []);
    $featureRows = is_array($featureRows) && $featureRows !== [] ? $featureRows : [[]];
    $contributionRows = old('contributions', $project->contributions ?? []);
    $contributionRows = is_array($contributionRows) && $contributionRows !== [] ? $contributionRows : [[]];
@endphp

<div class="grid gap-6 sm:grid-cols-2">
    <div>
        <label for="slug" class="{{ $labelClass }}">Slug</label>
        <input id="slug" name="slug" type="text" value="{{ old('slug', $project->slug) }}" class="{{ $inputClass }}" required>
        @error('slug')
            <p class="{{ $errorClass }}">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="status" class="{{ $labelClass }}">Status</label>
        <select id="status" name="status" class="{{ $inputClass }}" required>
            @foreach (['production' => 'Production', 'in_progress' => 'In progress'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $project->status) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')
            <p class="{{ $errorClass }}">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="title_uz" class="{{ $labelClass }}">Title (Uzbek)</label>
        <input id="title_uz" name="title_uz" type="text" value="{{ old('title_uz', $project->title_uz) }}" class="{{ $inputClass }}" required>
        @error('title_uz')
            <p class="{{ $errorClass }}">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="title_en" class="{{ $labelClass }}">Title (English)</label>
        <input id="title_en" name="title_en" type="text" value="{{ old('title_en', $project->title_en) }}" class="{{ $inputClass }}" required>
        @error('title_en')
            <p class="{{ $errorClass }}">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="live_url" class="{{ $labelClass }}">Live URL</label>
        <input id="live_url" name="live_url" type="url" value="{{ old('live_url', $project->live_url) }}" class="{{ $inputClass }}">
        @error('live_url')
            <p class="{{ $errorClass }}">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="github_url" class="{{ $labelClass }}">GitHub URL</label>
        <input id="github_url" name="github_url" type="url" value="{{ old('github_url', $project->github_url) }}" class="{{ $inputClass }}">
        @error('github_url')
            <p class="{{ $errorClass }}">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="sort_order" class="{{ $labelClass }}">Sort order</label>
        <input id="sort_order" name="sort_order" type="number" min="0" max="10000" value="{{ old('sort_order', $project->sort_order ?? 0) }}" class="{{ $inputClass }}" required>
        @error('sort_order')
            <p class="{{ $errorClass }}">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="grid gap-6 sm:grid-cols-2">
    <div>
        <label for="summary_uz" class="{{ $labelClass }}">Summary (Uzbek)</label>
        <textarea id="summary_uz" name="summary_uz" rows="3" class="{{ $inputClass }}">{{ old('summary_uz', $project->summary_uz) }}</textarea>
        @error('summary_uz')
            <p class="{{ $errorClass }}">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="summary_en" class="{{ $labelClass }}">Summary (English)</label>
        <textarea id="summary_en" name="summary_en" rows="3" class="{{ $inputClass }}">{{ old('summary_en', $project->summary_en) }}</textarea>
        @error('summary_en')
            <p class="{{ $errorClass }}">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description_uz" class="{{ $labelClass }}">Description (Uzbek)</label>
        <textarea id="description_uz" name="description_uz" rows="6" class="{{ $inputClass }}">{{ old('description_uz', $project->description_uz) }}</textarea>
        @error('description_uz')
            <p class="{{ $errorClass }}">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description_en" class="{{ $labelClass }}">Description (English)</label>
        <textarea id="description_en" name="description_en" rows="6" class="{{ $inputClass }}">{{ old('description_en', $project->description_en) }}</textarea>
        @error('description_en')
            <p class="{{ $errorClass }}">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="grid gap-6 sm:grid-cols-2">
    <div>
        <label for="problem_uz" class="{{ $labelClass }}">Problem (Uzbek)</label>
        <textarea id="problem_uz" name="problem_uz" rows="4" class="{{ $inputClass }}">{{ old('problem_uz', $project->problem_uz) }}</textarea>
        @error('problem_uz')
            <p class="{{ $errorClass }}">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="problem_en" class="{{ $labelClass }}">Problem (English)</label>
        <textarea id="problem_en" name="problem_en" rows="4" class="{{ $inputClass }}">{{ old('problem_en', $project->problem_en) }}</textarea>
        @error('problem_en')
            <p class="{{ $errorClass }}">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="solution_uz" class="{{ $labelClass }}">Solution (Uzbek)</label>
        <textarea id="solution_uz" name="solution_uz" rows="4" class="{{ $inputClass }}">{{ old('solution_uz', $project->solution_uz) }}</textarea>
        @error('solution_uz')
            <p class="{{ $errorClass }}">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="solution_en" class="{{ $labelClass }}">Solution (English)</label>
        <textarea id="solution_en" name="solution_en" rows="4" class="{{ $inputClass }}">{{ old('solution_en', $project->solution_en) }}</textarea>
        @error('solution_en')
            <p class="{{ $errorClass }}">{{ $message }}</p>
        @enderror
    </div>
</div>

<fieldset class="rounded-2xl border border-white/10 p-4" data-repeater>
    <legend class="px-1 text-sm font-medium text-foreground">Key features</legend>

    <div class="mt-4 space-y-4" data-repeater-items>
        @foreach ($featureRows as $index => $feature)
            <div class="rounded-2xl border border-white/10 p-4" data-repeater-item>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="{{ $labelClass }}">Title (Uzbek)</label>
                        <input name="features[{{ $index }}][title_uz]" type="text" value="{{ $feature['title_uz'] ?? '' }}" class="{{ $inputClass }}">
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Title (English)</label>
                        <input name="features[{{ $index }}][title_en]" type="text" value="{{ $feature['title_en'] ?? '' }}" class="{{ $inputClass }}">
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Description (Uzbek)</label>
                        <textarea name="features[{{ $index }}][description_uz]" rows="3" class="{{ $inputClass }}">{{ $feature['description_uz'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Description (English)</label>
                        <textarea name="features[{{ $index }}][description_en]" rows="3" class="{{ $inputClass }}">{{ $feature['description_en'] ?? '' }}</textarea>
                    </div>
                </div>
                <button type="button" data-repeater-remove class="mt-3 text-sm text-red-400 transition hover:text-red-300">Remove</button>
            </div>
        @endforeach
    </div>

    <template data-repeater-template>
        <div class="rounded-2xl border border-white/10 p-4" data-repeater-item>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="{{ $labelClass }}">Title (Uzbek)</label>
                    <input name="features[__INDEX__][title_uz]" type="text" value="" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}">Title (English)</label>
                    <input name="features[__INDEX__][title_en]" type="text" value="" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}">Description (Uzbek)</label>
                    <textarea name="features[__INDEX__][description_uz]" rows="3" class="{{ $inputClass }}"></textarea>
                </div>
                <div>
                    <label class="{{ $labelClass }}">Description (English)</label>
                    <textarea name="features[__INDEX__][description_en]" rows="3" class="{{ $inputClass }}"></textarea>
                </div>
            </div>
            <button type="button" data-repeater-remove class="mt-3 text-sm text-red-400 transition hover:text-red-300">Remove</button>
        </div>
    </template>

    <button type="button" data-repeater-add class="btn btn-secondary mt-4">Add feature</button>

    @error('features')
        <p class="{{ $errorClass }}">{{ $message }}</p>
    @enderror
    @error('features.*')
        <p class="{{ $errorClass }}">{{ $message }}</p>
    @enderror
</fieldset>

<fieldset class="rounded-2xl border border-white/10 p-4" data-repeater>
    <legend class="px-1 text-sm font-medium text-foreground">My contribution</legend>

    <div class="mt-4 space-y-4" data-repeater-items>
        @foreach ($contributionRows as $index => $contribution)
            <div class="rounded-2xl border border-white/10 p-4" data-repeater-item>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="{{ $labelClass }}">Title (Uzbek)</label>
                        <input name="contributions[{{ $index }}][title_uz]" type="text" value="{{ $contribution['title_uz'] ?? '' }}" class="{{ $inputClass }}">
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Title (English)</label>
                        <input name="contributions[{{ $index }}][title_en]" type="text" value="{{ $contribution['title_en'] ?? '' }}" class="{{ $inputClass }}">
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Description (Uzbek)</label>
                        <textarea name="contributions[{{ $index }}][description_uz]" rows="3" class="{{ $inputClass }}">{{ $contribution['description_uz'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Description (English)</label>
                        <textarea name="contributions[{{ $index }}][description_en]" rows="3" class="{{ $inputClass }}">{{ $contribution['description_en'] ?? '' }}</textarea>
                    </div>
                </div>
                <button type="button" data-repeater-remove class="mt-3 text-sm text-red-400 transition hover:text-red-300">Remove</button>
            </div>
        @endforeach
    </div>

    <template data-repeater-template>
        <div class="rounded-2xl border border-white/10 p-4" data-repeater-item>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="{{ $labelClass }}">Title (Uzbek)</label>
                    <input name="contributions[__INDEX__][title_uz]" type="text" value="" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}">Title (English)</label>
                    <input name="contributions[__INDEX__][title_en]" type="text" value="" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}">Description (Uzbek)</label>
                    <textarea name="contributions[__INDEX__][description_uz]" rows="3" class="{{ $inputClass }}"></textarea>
                </div>
                <div>
                    <label class="{{ $labelClass }}">Description (English)</label>
                    <textarea name="contributions[__INDEX__][description_en]" rows="3" class="{{ $inputClass }}"></textarea>
                </div>
            </div>
            <button type="button" data-repeater-remove class="mt-3 text-sm text-red-400 transition hover:text-red-300">Remove</button>
        </div>
    </template>

    <button type="button" data-repeater-add class="btn btn-secondary mt-4">Add contribution</button>

    @error('contributions')
        <p class="{{ $errorClass }}">{{ $message }}</p>
    @enderror
    @error('contributions.*')
        <p class="{{ $errorClass }}">{{ $message }}</p>
    @enderror
</fieldset>

<fieldset class="rounded-2xl border border-white/10 p-4">
    <legend class="px-1 text-sm font-medium text-foreground">Visibility</legend>
    <div class="mt-2 flex flex-wrap gap-6">
        <label class="flex items-center gap-2 text-sm text-muted">
            <input type="checkbox" name="is_featured" value="1" class="size-4 rounded border-white/10 bg-background accent-accent" @checked(old('is_featured', $project->is_featured))>
            Featured
        </label>
        <label class="flex items-center gap-2 text-sm text-muted">
            <input type="checkbox" name="is_published" value="1" class="size-4 rounded border-white/10 bg-background accent-accent" @checked(old('is_published', $project->is_published))>
            Published
        </label>
    </div>
    @error('is_featured')
        <p class="{{ $errorClass }}">{{ $message }}</p>
    @enderror
    @error('is_published')
        <p class="{{ $errorClass }}">{{ $message }}</p>
    @enderror
</fieldset>

<fieldset class="rounded-2xl border border-white/10 p-4">
    <legend class="px-1 text-sm font-medium text-foreground">Technologies</legend>
    <div class="mt-2 flex flex-wrap gap-x-6 gap-y-3">
        @forelse ($technologies as $technology)
            <label class="flex items-center gap-2 text-sm text-muted">
                <input type="checkbox" name="technologies[]" value="{{ $technology->id }}" class="size-4 rounded border-white/10 bg-background accent-accent" @checked(in_array($technology->id, $selectedTechnologies))>
                {{ $technology->name }}
            </label>
        @empty
            <p class="text-sm text-muted">No technologies available.</p>
        @endforelse
    </div>
    @error('technologies')
        <p class="{{ $errorClass }}">{{ $message }}</p>
    @enderror
    @error('technologies.*')
        <p class="{{ $errorClass }}">{{ $message }}</p>
    @enderror
</fieldset>

<div>
    <label for="screenshots" class="{{ $labelClass }}">Screenshots</label>
    <input id="screenshots" name="screenshots[]" type="file" accept="image/png,image/jpeg,image/webp" multiple class="mt-1 block w-full text-sm text-muted file:mr-4 file:rounded-full file:border-0 file:bg-accent file:px-4 file:py-2 file:text-sm file:font-medium file:text-background">
    <p class="mt-1 text-xs text-muted">PNG, JPG, JPEG or WebP, max 2 MB each, max 10 screenshots per project.</p>
    @error('screenshots')
        <p class="{{ $errorClass }}">{{ $message }}</p>
    @enderror
    @error('screenshots.*')
        <p class="{{ $errorClass }}">{{ $message }}</p>
    @enderror
</div>

@if ($project->exists)
    <fieldset class="rounded-2xl border border-white/10 p-4">
        <legend class="px-1 text-sm font-medium text-foreground">Existing screenshots</legend>
        <div class="mt-2 space-y-4">
            @forelse ($project->images as $image)
                <div class="rounded-2xl border border-white/10 p-4">
                    <div class="flex flex-col gap-4 sm:flex-row">
                        @if (\Illuminate\Support\Facades\Storage::disk('public')->exists($image->path))
                            @php
                                $existingSize = @getimagesize(\Illuminate\Support\Facades\Storage::disk('public')->path($image->path));
                            @endphp
                            <img src="{{ asset('storage/'.$image->path) }}" @if ($existingSize) width="{{ $existingSize[0] }}" height="{{ $existingSize[1] }}" @endif alt="{{ $image->localizedAlt() }}" class="h-24 w-40 rounded-xl border border-white/10 object-cover">
                        @endif

                        <div class="grid flex-1 gap-3 sm:grid-cols-2">
                            <div>
                                <label for="image_{{ $image->id }}_alt_uz" class="{{ $labelClass }}">Alt text (Uzbek)</label>
                                <input id="image_{{ $image->id }}_alt_uz" name="images[{{ $image->id }}][alt_uz]" type="text" value="{{ old('images.'.$image->id.'.alt_uz', $image->alt_uz) }}" class="{{ $inputClass }}">
                                @error('images.'.$image->id.'.alt_uz')
                                    <p class="{{ $errorClass }}">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="image_{{ $image->id }}_alt_en" class="{{ $labelClass }}">Alt text (English)</label>
                                <input id="image_{{ $image->id }}_alt_en" name="images[{{ $image->id }}][alt_en]" type="text" value="{{ old('images.'.$image->id.'.alt_en', $image->alt_en) }}" class="{{ $inputClass }}">
                                @error('images.'.$image->id.'.alt_en')
                                    <p class="{{ $errorClass }}">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="image_{{ $image->id }}_sort_order" class="{{ $labelClass }}">Sort order</label>
                                <input id="image_{{ $image->id }}_sort_order" name="images[{{ $image->id }}][sort_order]" type="number" min="0" max="10000" value="{{ old('images.'.$image->id.'.sort_order', $image->sort_order) }}" class="{{ $inputClass }}" required>
                                @error('images.'.$image->id.'.sort_order')
                                    <p class="{{ $errorClass }}">{{ $message }}</p>
                                @enderror
                            </div>

                            <label class="flex items-center gap-2 text-sm text-muted sm:col-span-2">
                                <input type="checkbox" name="delete_screenshots[]" value="{{ $image->id }}" class="size-4 rounded border-white/10 bg-background accent-accent" @checked(in_array($image->id, $selectedScreenshots))>
                                Delete screenshot {{ $loop->iteration }}
                            </label>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-muted">No screenshots yet.</p>
            @endforelse
        </div>
        @error('delete_screenshots')
            <p class="{{ $errorClass }}">{{ $message }}</p>
        @enderror
        @error('delete_screenshots.*')
            <p class="{{ $errorClass }}">{{ $message }}</p>
        @enderror
    </fieldset>
@endif

<div>
    <button type="submit" class="btn btn-primary">Save project</button>
</div>
