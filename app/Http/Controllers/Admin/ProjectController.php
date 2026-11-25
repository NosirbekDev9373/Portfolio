<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Technology;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function index(): View
    {
        $projects = Project::query()
            ->with(['technologies', 'images'])
            ->orderBy('sort_order')
            ->get();

        return view('admin.projects.index', compact('projects'));
    }

    public function create(): View
    {
        return view('admin.projects.create', [
            'project' => new Project,
            'technologies' => Technology::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->normalizeRepeatableInputs($request);
        $this->validateProject($request);

        $project = Project::create($this->projectData($request));

        $project->technologies()->sync($request->input('technologies', []));
        $this->handleScreenshots($request, $project);

        return redirect()->route('admin.projects.index')->with('status', 'Project created.');
    }

    public function edit(Project $project): View
    {
        return view('admin.projects.edit', [
            'project' => $project->load(['technologies', 'images']),
            'technologies' => Technology::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $this->normalizeRepeatableInputs($request);
        $this->validateProject($request, $project);

        $project->update($this->projectData($request));

        $project->technologies()->sync($request->input('technologies', []));
        $this->deleteScreenshots($request, $project);
        $this->updateScreenshotDetails($request, $project);
        $this->handleScreenshots($request, $project);

        return redirect()->route('admin.projects.index')->with('status', 'Project updated.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        foreach ($project->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $project->delete();

        return redirect()->route('admin.projects.index')->with('status', 'Project deleted.');
    }

    private function validateProject(Request $request, ?Project $project = null): void
    {
        $request->validate([
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('projects', 'slug')->ignore($project),
            ],
            'title_uz' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'summary_uz' => ['nullable', 'string', 'max:1000'],
            'summary_en' => ['nullable', 'string', 'max:1000'],
            'description_uz' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'problem_uz' => ['nullable', 'string'],
            'problem_en' => ['nullable', 'string'],
            'solution_uz' => ['nullable', 'string'],
            'solution_en' => ['nullable', 'string'],
            'features' => ['nullable', 'array'],
            'features.*' => ['array'],
            'features.*.title_uz' => ['required', 'string', 'max:255'],
            'features.*.title_en' => ['required', 'string', 'max:255'],
            'features.*.description_uz' => ['required', 'string', 'max:2000'],
            'features.*.description_en' => ['required', 'string', 'max:2000'],
            'contributions' => ['nullable', 'array'],
            'contributions.*' => ['array'],
            'contributions.*.title_uz' => ['required', 'string', 'max:255'],
            'contributions.*.title_en' => ['required', 'string', 'max:255'],
            'contributions.*.description_uz' => ['required', 'string', 'max:2000'],
            'contributions.*.description_en' => ['required', 'string', 'max:2000'],
            'status' => ['required', Rule::in(['production', 'in_progress'])],
            'live_url' => ['nullable', 'url', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:10000'],
            'is_featured' => ['boolean'],
            'is_published' => ['boolean'],
            'technologies' => ['array'],
            'technologies.*' => ['exists:technologies,id'],
            'screenshot' => ['nullable', 'file', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'screenshot_alt_uz' => ['nullable', 'string', 'max:255'],
            'screenshot_alt_en' => ['nullable', 'string', 'max:255'],
            'screenshot_sort_order' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'delete_screenshots' => ['array'],
            'delete_screenshots.*' => ['exists:project_images,id'],
            'images' => ['nullable', 'array'],
            'images.*' => ['array'],
            'images.*.alt_uz' => ['nullable', 'string', 'max:255'],
            'images.*.alt_en' => ['nullable', 'string', 'max:255'],
            'images.*.sort_order' => ['required', 'integer', 'min:0', 'max:10000'],
        ]);
    }

    private function projectData(Request $request): array
    {
        return array_merge(
            $request->only([
                'slug',
                'title_uz',
                'title_en',
                'summary_uz',
                'summary_en',
                'description_uz',
                'description_en',
                'problem_uz',
                'problem_en',
                'solution_uz',
                'solution_en',
                'status',
                'live_url',
                'github_url',
                'sort_order',
            ]),
            [
                'is_featured' => $request->boolean('is_featured'),
                'is_published' => $request->boolean('is_published'),
                'features' => $this->normalizedItems($request->input('features')),
                'contributions' => $this->normalizedItems($request->input('contributions')),
            ],
        );
    }

    private function normalizeRepeatableInputs(Request $request): void
    {
        $normalized = [];

        foreach (['features', 'contributions'] as $field) {
            $rows = $request->input($field);
            $rows = is_array($rows) ? $rows : [];

            $normalizedRows = [];

            foreach ($rows as $row) {
                if (! is_array($row)) {
                    continue;
                }

                $normalizedRow = [];

                foreach (['title_uz', 'title_en', 'description_uz', 'description_en'] as $key) {
                    $value = $row[$key] ?? '';
                    $normalizedRow[$key] = is_string($value) ? trim($value) : '';
                }

                if (implode('', $normalizedRow) === '') {
                    continue;
                }

                $normalizedRows[] = $normalizedRow;
            }

            $normalized[$field] = array_values($normalizedRows);
        }

        $request->merge($normalized);
    }

    /**
     * @return array<int, array{title_uz: string, title_en: string, description_uz: string, description_en: string}>|null
     */
    private function normalizedItems(mixed $items): ?array
    {
        if (! is_array($items) || $items === []) {
            return null;
        }

        return $items;
    }

    private function updateScreenshotDetails(Request $request, Project $project): void
    {
        $details = $request->input('images', []);

        if (! is_array($details)) {
            return;
        }

        foreach ($details as $id => $data) {
            if (! is_array($data)) {
                continue;
            }

            $image = $project->images()->whereKey($id)->first();

            if (! $image) {
                continue;
            }

            $altUz = trim((string) ($data['alt_uz'] ?? ''));
            $altEn = trim((string) ($data['alt_en'] ?? ''));

            $image->update([
                'alt_uz' => $altUz !== '' ? $altUz : null,
                'alt_en' => $altEn !== '' ? $altEn : null,
                'sort_order' => isset($data['sort_order']) && is_numeric($data['sort_order'])
                    ? (int) $data['sort_order']
                    : $image->sort_order,
            ]);
        }
    }

    private function handleScreenshots(Request $request, Project $project): void
    {
        if (! $request->hasFile('screenshot')) {
            return;
        }

        $path = $request->file('screenshot')->store('projects', 'public');

        $project->images()->create([
            'path' => $path,
            'alt_uz' => $this->imageAlt($request, 'screenshot_alt_uz', $project->title_uz),
            'alt_en' => $this->imageAlt($request, 'screenshot_alt_en', $project->title_en),
            'sort_order' => $request->filled('screenshot_sort_order')
                ? (int) $request->input('screenshot_sort_order')
                : (int) $project->images()->max('sort_order') + 1,
        ]);
    }

    private function imageAlt(Request $request, string $key, string $fallback): string
    {
        $value = trim((string) $request->input($key, ''));

        return $value !== '' ? $value : $fallback;
    }

    private function deleteScreenshots(Request $request, Project $project): void
    {
        $ids = $request->input('delete_screenshots', []);

        if (! is_array($ids)) {
            return;
        }

        foreach ($project->images()->whereIn('id', $ids)->get() as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }
    }
}
