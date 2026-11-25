<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Technology;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class HomeController extends Controller
{
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

        $technologiesByGroup = Technology::query()
            ->whereHas('projects', fn (Builder $query) => $query->published())
            ->orderBy('name')
            ->get()
            ->groupBy('group');

        $repositories = Project::query()
            ->published()
            ->whereNotNull('github_url')
            ->orderBy('sort_order')
            ->get();

        return view('home', compact(
            'featuredProjects',
            'productionProjectsCount',
            'technologiesByGroup',
            'repositories',
        ));
    }
}
