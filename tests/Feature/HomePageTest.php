<?php

namespace Tests\Feature;

use App\Models\Project;
use Database\Seeders\ProjectSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_published_featured_projects_from_the_database(): void
    {
        $this->seed(ProjectSeeder::class);

        $this->get('/uz')
            ->assertOk()
            ->assertSee('Steak House')
            ->assertSee('Tozalash Web Sayt')
            ->assertSee('https://steakhouse.nweb.uz/', false)
            ->assertSee('https://work.nweb.uz/', false)
            ->assertSee('PHP')
            ->assertSee('PostgreSQL');
    }

    public function test_home_page_renders_english_project_content(): void
    {
        $this->seed(ProjectSeeder::class);

        $this->get('/en')
            ->assertOk()
            ->assertSee('Cleaning Services Website')
            ->assertSee('Personal projects deployed to production.');
    }

    public function test_projects_index_shows_only_published_projects(): void
    {
        $this->seed(ProjectSeeder::class);

        Project::create([
            'slug' => 'hidden-project',
            'title_uz' => 'Yashirin loyiha',
            'title_en' => 'Hidden Project',
            'status' => 'production',
            'is_published' => false,
            'sort_order' => 3,
        ]);

        $this->get('/uz/projects')
            ->assertOk()
            ->assertSee('Steak House')
            ->assertSee('Tozalash Web Sayt')
            ->assertDontSee('Yashirin loyiha')
            ->assertSee('property="og:type" content="website"', false);
    }

    public function test_project_case_study_renders_all_sections_from_published_project(): void
    {
        $this->seed(ProjectSeeder::class);

        $this->get('/en/projects/steak-house')
            ->assertOk()
            ->assertSee('Steak House')
            ->assertSeeText('Overview')
            ->assertSeeText('Problem & Solution')
            ->assertSeeText('Key Features')
            ->assertSeeText('Technology Stack')
            ->assertSeeText('Screenshots')
            ->assertSeeText('My Contribution')
            ->assertSeeText('Live Demo / Repository')
            ->assertSeeText('Repository is not public.')
            ->assertSee('https://steakhouse.nweb.uz/', false)
            ->assertSee('<title>Steak House — Case Study</title>', false)
            ->assertSee('property="og:type" content="article"', false)
            ->assertSee('property="og:title"', false)
            ->assertSee('property="og:description"', false)
            ->assertSee('property="og:url"', false)
            ->assertSee('name="twitter:card"', false);

        $this->get('/uz/projects/steak-house')
            ->assertOk()
            ->assertSee('Steak House')
            ->assertSee('<link rel="alternate" hreflang="en" href="'.url('/en/projects/steak-house').'"', false);
    }

    public function test_unpublished_and_missing_projects_return_custom_404(): void
    {
        Project::create([
            'slug' => 'hidden-project',
            'title_uz' => 'Yashirin loyiha',
            'title_en' => 'Hidden Project',
            'status' => 'production',
            'is_published' => false,
        ]);

        $this->get('/uz/projects/hidden-project')
            ->assertNotFound()
            ->assertSee('Sahifa topilmadi')
            ->assertSee('name="robots" content="noindex"', false);

        $this->get('/uz/projects/does-not-exist')
            ->assertNotFound()
            ->assertSee('Sahifa topilmadi');

        $this->get('/en/unknown-page')
            ->assertNotFound()
            ->assertSee('Page not found');
    }

    public function test_sitemap_contains_localized_published_project_urls(): void
    {
        $this->seed(ProjectSeeder::class);

        Project::create([
            'slug' => 'hidden-project',
            'title_uz' => 'Yashirin loyiha',
            'title_en' => 'Hidden Project',
            'status' => 'production',
            'is_published' => false,
        ]);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<?xml version="1.0" encoding="UTF-8"?>', false)
            ->assertSee('<loc>'.url('/uz').'</loc>', false)
            ->assertSee('<loc>'.url('/en/projects').'</loc>', false)
            ->assertSee('<loc>'.url('/uz/projects/steak-house').'</loc>', false)
            ->assertSee('<loc>'.url('/en/projects/tozalash-web-sayt').'</loc>', false)
            ->assertDontSee('hidden-project');
    }

    public function test_case_study_renders_real_screenshot_when_file_exists(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('projects/test-screen.png', 'fake-image');

        $project = Project::create([
            'slug' => 'screen-project',
            'title_uz' => 'Ekran loyihasi',
            'title_en' => 'Screen Project',
            'status' => 'production',
            'is_published' => true,
        ]);

        $project->images()->create([
            'path' => 'projects/test-screen.png',
            'alt_uz' => 'Ekran',
            'alt_en' => 'Screen',
        ]);

        $this->get('/en/projects/screen-project')
            ->assertOk()
            ->assertSee('projects/test-screen.png', false)
            ->assertSee('property="og:image"', false);
    }

    public function test_robots_txt_uses_absolute_sitemap_url(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Sitemap: '.url('/sitemap.xml'));
    }
}
