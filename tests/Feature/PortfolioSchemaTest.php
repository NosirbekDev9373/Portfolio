<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\Technology;
use Database\Seeders\ProjectSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PortfolioSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_portfolio_tables_are_created(): void
    {
        $this->assertTrue(Schema::hasTable('projects'));
        $this->assertTrue(Schema::hasTable('technologies'));
        $this->assertTrue(Schema::hasTable('project_technology'));
        $this->assertTrue(Schema::hasTable('project_images'));
    }

    public function test_project_relationships_and_scopes_work(): void
    {
        $project = Project::create([
            'slug' => 'test-project',
            'title_uz' => 'Test loyiha',
            'title_en' => 'Test project',
            'status' => 'production',
            'is_featured' => true,
            'is_published' => true,
        ]);

        $technology = Technology::create([
            'name' => 'Laravel',
            'group' => 'backend',
        ]);

        $project->technologies()->attach($technology);

        $image = $project->images()->create([
            'path' => 'projects/test.png',
            'alt_uz' => 'Test rasm',
            'alt_en' => 'Test image',
        ]);

        $this->assertTrue($project->technologies->contains($technology));
        $this->assertTrue($project->images->contains($image));
        $this->assertTrue($technology->projects->contains($project));
        $this->assertSame($project->id, $image->project->id);
        $this->assertCount(1, Project::published()->get());
        $this->assertCount(1, Project::featured()->get());
    }

    public function test_project_seeder_creates_the_two_production_projects(): void
    {
        $this->seed(ProjectSeeder::class);

        $this->assertSame(2, Project::count());
        $this->assertSame(2, ProjectImage::count());
        $this->assertSame(10, Technology::count());

        $steakHouse = Project::where('slug', 'steak-house')->firstOrFail();
        $tozalash = Project::where('slug', 'tozalash-web-sayt')->firstOrFail();

        $this->assertSame('production', $steakHouse->status);
        $this->assertSame('https://steakhouse.nweb.uz/', $steakHouse->live_url);
        $this->assertNull($steakHouse->github_url);
        $this->assertTrue($steakHouse->is_published);
        $this->assertTrue($steakHouse->is_featured);
        $this->assertEqualsCanonicalizing(
            ['PHP', 'Laravel', 'Blade', 'SQLite', 'Bootstrap', 'PHPUnit'],
            $steakHouse->technologies->pluck('name')->all(),
        );
        $this->assertNotContains('Tailwind CSS', $steakHouse->technologies->pluck('name')->all());

        $this->assertSame('production', $tozalash->status);
        $this->assertSame('https://work.nweb.uz/', $tozalash->live_url);
        $this->assertSame('https://github.com/NosirbekDev9373/Laravel-for-testing-website', $tozalash->github_url);
        $this->assertEqualsCanonicalizing(
            ['PHP', 'Laravel', 'Tailwind CSS', 'Vite', 'Sanctum', 'PHPUnit', 'PostgreSQL'],
            $tozalash->technologies->pluck('name')->all(),
        );
    }
}
