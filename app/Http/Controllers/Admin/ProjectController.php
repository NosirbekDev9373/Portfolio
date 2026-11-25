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
        $this->validateProject($request, $project);

        $project->update($this->projectData($request));

        $project->technologies()->sync($request->input('technologies', []));
        $this->deleteScreenshots($request, $project);
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
            'status' => ['required', Rule::in(['production', 'in_progress'])],
            'live_url' => ['nullable', 'url', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:10000'],
            'is_featured' => ['boolean'],
            'is_published' => ['boolean'],
            'technologies' => ['array'],
            'technologies.*' => ['exists:technologies,id'],
            'screenshot' => ['nullable', 'file', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'delete_screenshots' => ['array'],
            'delete_screenshots.*' => ['exists:project_images,id'],
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
                'status',
                'live_url',
                'github_url',
                'sort_order',
            ]),
            [
                'is_featured' => $request->boolean('is_featured'),
                'is_published' => $request->boolean('is_published'),
            ],
        );
    }

    private function handleScreenshots(Request $request, Project $project): void
    {
        if (! $request->hasFile('screenshot')) {
            return;
        }

        $path = $request->file('screenshot')->store('projects', 'public');

        $project->images()->create([
            'path' => $path,
            'alt_uz' => $project->title_uz,
            'alt_en' => $project->title_en,
            'sort_order' => (int) $project->images()->max('sort_order') + 1,
        ]);
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
