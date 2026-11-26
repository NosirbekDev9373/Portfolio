<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Skill;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    private const SKILL_GROUP_ORDER = ['backend', 'frontend', 'database', 'tools', 'testing'];

    public function index(): View
    {
        $featuredProjects = Project::query()
            ->published()
            ->featured()
            ->with(['technologies', 'images'])
            ->orderBy('sort_order')
            ->get();

        $productionProjectsCount = Project::query()
            ->published()
            ->where('status', 'production')
            ->count();

        $skills = Skill::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('group');

        $skillsByGroup = collect(self::SKILL_GROUP_ORDER)
            ->filter(fn (string $group): bool => $skills->has($group))
            ->mapWithKeys(fn (string $group): array => [$group => $skills->get($group)]);

        $repositories = Project::query()
            ->published()
            ->whereNotNull('github_url')
            ->orderBy('sort_order')
            ->get();

        return view('home', compact(
            'featuredProjects',
            'productionProjectsCount',
            'skillsByGroup',
            'repositories',
        ));
    }
}
