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

        Project::where('slug', 'steak-house')->firstOrFail()->update([
            'problem_uz' => 'Uzbek problem text.',
            'problem_en' => 'English problem text.',
            'solution_uz' => 'Uzbek solution text.',
            'solution_en' => 'English solution text.',
            'features' => [
                [
                    'title_uz' => 'Uzbek feature title',
                    'title_en' => 'English feature title',
                    'description_uz' => 'Uzbek feature description',
                    'description_en' => 'English feature description',
                ],
            ],
            'contributions' => [
                [
                    'title_uz' => 'Uzbek contribution title',
                    'title_en' => 'English contribution title',
                    'description_uz' => 'Uzbek contribution description',
                    'description_en' => 'English contribution description',
                ],
            ],
        ]);

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
            ->assertSeeText('English problem text.')
            ->assertSeeText('English solution text.')
            ->assertSeeText('English feature title')
            ->assertSeeText('English feature description')
            ->assertSeeText('English contribution title')
            ->assertSeeText('English contribution description')
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

    public function test_case_study_hides_empty_case_study_sections(): void
    {
        $this->seed(ProjectSeeder::class);

        $this->get('/en/projects/steak-house')
            ->assertOk()
            ->assertSeeText('Overview')
            ->assertDontSeeText('Problem & Solution')
            ->assertDontSeeText('Key Features')
            ->assertDontSeeText('My Contribution')
            ->assertDontSee('[Problem description will be added.]')
            ->assertDontSee('[Solution description will be added.]')
            ->assertDontSee('[Key feature')
            ->assertDontSee('[Contribution item');
    }

    public function test_case_study_omits_overview_when_description_and_summary_are_missing(): void
    {
        Project::create([
            'slug' => 'no-overview',
            'title_uz' => 'Tavsifsiz loyiha',
            'title_en' => 'Project Without Description',
            'status' => 'production',
            'is_published' => true,
        ]);

        $this->get('/en/projects/no-overview')
            ->assertOk()
            ->assertDontSeeText('Overview')
            ->assertDontSee('[Overview text will be added.]', false);
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

    public function test_project_cards_show_uploaded_screenshots(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('projects/card.png', 'fake-image');

        $project = Project::create([
            'slug' => 'card-image',
            'title_uz' => 'Kartadagi loyiha',
            'title_en' => 'Card Project',
            'status' => 'production',
            'is_featured' => true,
            'is_published' => true,
        ]);

        $project->images()->create([
            'path' => 'projects/card.png',
            'alt_uz' => 'Karta',
            'alt_en' => 'Card',
        ]);

        $this->get('/uz')
            ->assertOk()
            ->assertSee(asset('storage/projects/card.png'), false)
            ->assertSee('alt="Karta"', false)
            ->assertDontSee('data-carousel-prev', false)
            ->assertDontSee('data-carousel-dots', false);

        $this->get('/uz/projects')
            ->assertOk()
            ->assertSee(asset('storage/projects/card.png'), false)
            ->assertDontSee('data-carousel-prev', false)
            ->assertDontSee('data-carousel-dots', false);
    }

    public function test_project_cards_render_every_uploaded_screenshot(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('projects/card-first.png', 'fake-image');
        Storage::disk('public')->put('projects/card-second.png', 'fake-image');

        $project = Project::create([
            'slug' => 'multi-card',
            'title_uz' => 'Ko‘p rasmlar',
            'title_en' => 'Multiple Images',
            'status' => 'production',
            'is_featured' => true,
            'is_published' => true,
        ]);

        $project->images()->create(['path' => 'projects/card-first.png', 'alt_uz' => 'Birinchi', 'alt_en' => 'First']);
        $project->images()->create(['path' => 'projects/card-second.png', 'alt_uz' => 'Ikkinchi', 'alt_en' => 'Second']);

        $home = $this->get('/uz')->assertOk();
        $index = $this->get('/uz/projects')->assertOk();

        $this->assertSame(2, substr_count($home->getContent(), '<img'));
        $this->assertSame(2, substr_count($index->getContent(), '<img'));
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

        $response = $this->get('/en/projects/screen-project')
            ->assertOk()
            ->assertSee('data-project-carousel', false)
            ->assertSee('data-carousel-track', false)
            ->assertSee('snap-x', false)
            ->assertSee(asset('storage/projects/test-screen.png'), false)
            ->assertSee('content="'.asset('storage/projects/test-screen.png').'"', false)
            ->assertSee('property="og:image"', false)
            ->assertSee('width="1600"', false)
            ->assertSee('height="1000"', false)
            ->assertSee('object-cover', false)
            ->assertSee('alt="Screen"', false)
            ->assertSee('loading="eager"', false)
            ->assertDontSee('loading="lazy"', false)
            ->assertDontSee('data-carousel-prev', false)
            ->assertDontSee('data-carousel-next', false)
            ->assertDontSee('data-carousel-dots', false)
            ->assertDontSee('data-carousel-autoplay', false);

        $this->assertSame(1, substr_count($response->getContent(), '<img'));
    }

    public function test_case_study_renders_carousel_controls_for_multiple_screenshots(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('projects/first.png', 'fake-image');
        Storage::disk('public')->put('projects/second.png', 'fake-image');

        $project = Project::create([
            'slug' => 'carousel-project',
            'title_uz' => 'Karussel loyihasi',
            'title_en' => 'Carousel Project',
            'status' => 'production',
            'is_published' => true,
        ]);

        $project->images()->create([
            'path' => 'projects/first.png',
            'alt_uz' => 'Birinchi',
            'alt_en' => 'First',
            'sort_order' => 1,
        ]);
        $project->images()->create([
            'path' => 'projects/second.png',
            'sort_order' => 2,
        ]);

        $response = $this->get('/en/projects/carousel-project')
            ->assertOk()
            ->assertSee('data-project-carousel', false)
            ->assertSee('data-carousel-prev', false)
            ->assertSee('data-carousel-next', false)
            ->assertSee('aria-label="Previous screenshot"', false)
            ->assertSee('aria-label="Next screenshot"', false)
            ->assertSee('data-carousel-autoplay', false)
            ->assertSee('data-carousel-dots', false)
            ->assertSee('data-carousel-dot', false)
            ->assertSee('aria-label="Screenshot dots"', false)
            ->assertSee('aria-label="Go to screenshot 1"', false)
            ->assertSee('aria-current="true"', false)
            ->assertSee('1 / 2')
            ->assertSee('alt="First"', false)
            ->assertSee('alt="Carousel Project"', false)
            ->assertSee(asset('storage/projects/first.png'), false)
            ->assertSee(asset('storage/projects/second.png'), false);

        $html = $response->getContent();

        $this->assertSame(2, substr_count($html, '<img'));
        $this->assertSame(1, substr_count($html, 'loading="eager"'));
        $this->assertSame(1, substr_count($html, 'loading="lazy"'));
        $this->assertSame(2, substr_count($html, 'width="1600"'));
        $this->assertSame(2, substr_count($html, 'height="1000"'));
    }

    public function test_carousel_skips_screenshot_records_without_files(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('projects/kept.png', 'fake-image');

        $project = Project::create([
            'slug' => 'missing-files-project',
            'title_uz' => 'Faylsiz loyiha',
            'title_en' => 'Missing Files Project',
            'status' => 'production',
            'is_published' => true,
        ]);

        $project->images()->create([
            'path' => 'projects/kept.png',
            'alt_uz' => 'Bor',
            'alt_en' => 'Kept',
        ]);
        $project->images()->create([
            'path' => 'projects/missing.png',
            'alt_uz' => 'Yo‘q',
            'alt_en' => 'Missing',
        ]);

        $this->get('/en/projects/missing-files-project')
            ->assertOk()
            ->assertSee(asset('storage/projects/kept.png'), false)
            ->assertDontSee(asset('storage/projects/missing.png'), false)
            ->assertDontSee('data-carousel-prev', false)
            ->assertDontSee('data-carousel-dots', false)
            ->assertDontSee('data-carousel-autoplay', false);
    }

    public function test_case_study_renders_carousel_placeholder_without_screenshots(): void
    {
        Project::create([
            'slug' => 'no-screenshots',
            'title_uz' => 'Skrinshotsiz loyiha',
            'title_en' => 'No Screenshots Project',
            'status' => 'production',
            'is_published' => true,
        ]);

        $response = $this->get('/en/projects/no-screenshots')
            ->assertOk()
            ->assertSee('data-project-carousel', false)
            ->assertSee('data-carousel-track', false)
            ->assertSee('Screenshot will be added')
            ->assertDontSee('data-carousel-prev', false)
            ->assertDontSee('data-carousel-next', false)
            ->assertDontSee('data-carousel-dots', false)
            ->assertDontSee('data-carousel-autoplay', false);

        $this->assertSame(0, substr_count($response->getContent(), '<img'));
    }

    public function test_robots_txt_uses_absolute_sitemap_url(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Disallow: /admin')
            ->assertSee('Disallow: /login')
            ->assertSee('Sitemap: '.url('/sitemap.xml'));
    }
}
