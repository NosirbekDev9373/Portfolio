<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index(): View
    {
        $projects = Project::query()
            ->published()
            ->with('technologies')
            ->orderBy('sort_order')
            ->get();

        return view('projects.index', compact('projects'));
    }

    public function show(string $locale, Project $project): View
    {
        abort_unless($project->is_published, 404);

        $project->load(['technologies', 'images']);

        $screenshots = $project->images
            ->filter(fn ($image) => Storage::disk('public')->exists($image->path))
            ->values();

        $technologiesByGroup = $project->technologies->groupBy('group');

        $ogImage = $screenshots->isNotEmpty()
            ? url(Storage::disk('public')->url($screenshots->first()->path))
            : null;

        return view('welcome', array_merge(compact(
            'project',
            'screenshots',
            'technologiesByGroup',
            'ogImage',
        ), ['mode' => 'show']));
    }
}
