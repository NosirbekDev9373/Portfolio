<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $locales = config('portfolio.locales.supported');

        $projects = Project::query()
            ->published()
            ->orderBy('sort_order')
            ->get(['slug', 'updated_at']);

        $urls = [];

        foreach ($locales as $locale) {
            $urls[] = ['loc' => route('home', ['locale' => $locale])];
            $urls[] = ['loc' => route('projects.index', ['locale' => $locale])];

            foreach ($projects as $project) {
                $urls[] = [
                    'loc' => route('projects.show', ['locale' => $locale, 'project' => $project->slug]),
                    'lastmod' => $project->updated_at?->toAtomString(),
                ];
            }
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $url) {
            $xml .= '  <url>'."\n";
            $xml .= '    <loc>'.e($url['loc']).'</loc>'."\n";

            if (! empty($url['lastmod'])) {
                $xml .= '    <lastmod>'.e($url['lastmod']).'</lastmod>'."\n";
            }

            $xml .= '  </url>'."\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
