<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\Skill;
use App\Models\Technology;
use Database\Seeders\ProjectSeeder;
use Database\Seeders\SkillSeeder;
use Illuminate\Database\QueryException;
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
        $this->assertTrue(Schema::hasTable('skills'));
    }

    public function test_skills_table_supports_defaults_grouping_and_ordering(): void
    {
        $skill = Skill::create([
            'name' => 'Laravel 13',
            'group' => 'backend',
        ]);

        $this->assertTrue($skill->exists);
        $this->assertSame('used', $skill->status);
        $this->assertSame(0, $skill->sort_order);
        $this->assertDatabaseHas('skills', [
            'name' => 'Laravel 13',
            'group' => 'backend',
            'status' => 'used',
            'sort_order' => 0,
        ]);
    }

    public function test_skill_name_is_unique_within_a_group(): void
    {
        Skill::create([
            'name' => 'PHP 8.3+',
            'group' => 'backend',
        ]);

        $this->expectException(QueryException::class);

        Skill::create([
            'name' => 'PHP 8.3+',
            'group' => 'backend',
        ]);
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

    public function test_project_images_are_ordered_by_sort_order(): void
    {
        $project = Project::create([
            'slug' => 'ordered-images',
            'title_uz' => 'Tartiblangan rasmlar',
            'title_en' => 'Ordered Images',
            'status' => 'production',
        ]);

        $project->images()->create([
            'path' => 'projects/later.png',
            'alt_uz' => 'Keyin',
            'alt_en' => 'Later',
            'sort_order' => 2,
        ]);
        $project->images()->create([
            'path' => 'projects/first.png',
            'alt_uz' => 'Birinchi',
            'alt_en' => 'First',
            'sort_order' => 1,
        ]);

        $this->assertSame(
            ['projects/first.png', 'projects/later.png'],
            $project->images->pluck('path')->all(),
        );
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

    public function test_project_seeder_does_not_overwrite_existing_project_data(): void
    {
        $project = Project::create([
            'slug' => 'steak-house',
            'title_uz' => 'Admin title',
            'title_en' => 'Admin title EN',
            'status' => 'in_progress',
            'is_featured' => false,
            'is_published' => false,
            'sort_order' => 99,
        ]);

        $technology = Technology::create([
            'name' => 'AdminTech',
            'group' => 'backend',
        ]);

        $project->technologies()->attach($technology);

        $project->images()->create([
            'path' => 'projects/admin.png',
            'alt_uz' => 'Admin rasm',
            'alt_en' => 'Admin image',
        ]);

        $this->seed(ProjectSeeder::class);

        $project->refresh()->load('technologies', 'images');

        $this->assertSame('Admin title', $project->title_uz);
        $this->assertSame('in_progress', $project->status);
        $this->assertFalse($project->is_featured);
        $this->assertFalse($project->is_published);
        $this->assertSame(99, $project->sort_order);
        $this->assertSame(['AdminTech'], $project->technologies->pluck('name')->all());
        $this->assertSame(['projects/admin.png'], $project->images->pluck('path')->all());
        $this->assertTrue(Project::where('slug', 'tozalash-web-sayt')->exists());
    }

    public function test_database_seeder_skips_projects_in_production(): void
    {
        $this->app['env'] = 'production';

        $this->seed();

        $this->assertSame(0, Project::count());
        $this->assertSame(0, Skill::count());
    }

    public function test_database_seeder_seeds_projects_outside_production(): void
    {
        $this->seed();

        $this->assertSame(2, Project::count());
        $this->assertSame(31, Skill::count());
    }

    public function test_skill_seeder_creates_the_initial_skill_list(): void
    {
        $this->seed(SkillSeeder::class);

        $this->assertSame(31, Skill::count());
        $this->assertDatabaseHas('skills', [
            'name' => 'PHP 8.3+',
            'group' => 'backend',
            'status' => 'used',
            'sort_order' => 1,
        ]);
        $this->assertDatabaseHas('skills', [
            'name' => 'Feature & Unit testing',
            'group' => 'testing',
            'status' => 'used',
            'sort_order' => 2,
        ]);
    }

    public function test_skill_seeder_does_not_overwrite_existing_skills(): void
    {
        $skill = Skill::create([
            'name' => 'PHP 8.3+',
            'group' => 'backend',
            'status' => 'learning',
            'sort_order' => 99,
        ]);

        $this->seed(SkillSeeder::class);

        $skill->refresh();

        $this->assertSame(31, Skill::count());
        $this->assertSame('learning', $skill->status);
        $this->assertSame(99, $skill->sort_order);
    }
}
