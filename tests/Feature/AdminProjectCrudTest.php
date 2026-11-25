<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Technology;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProjectCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Technology $technology;

    private Technology $otherTechnology;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->admin = User::factory()->create([
            'email' => config('portfolio.admin.email'),
            'password' => config('portfolio.admin.password'),
        ]);

        $this->technology = Technology::create(['name' => 'Laravel', 'group' => 'backend']);
        $this->otherTechnology = Technology::create(['name' => 'PostgreSQL', 'group' => 'database']);
    }

    public function test_admin_index_lists_projects(): void
    {
        $project = $this->createProject(['slug' => 'listed-project', 'title_en' => 'Listed Project']);

        $this->actingAs($this->admin)
            ->get(route('admin.projects.index'))
            ->assertOk()
            ->assertSee('Listed Project')
            ->assertSee('listed-project');

        $this->assertDatabaseHas('projects', ['id' => $project->id]);
    }

    public function test_admin_can_create_project_with_all_fields(): void
    {
        $payload = $this->payload([
            'slug' => 'new-project',
            'title_uz' => 'Yangi loyiha',
            'title_en' => 'New Project',
            'summary_uz' => 'Uzbek summary',
            'summary_en' => 'English summary',
            'description_uz' => 'Uzbek description',
            'description_en' => 'English description',
            'status' => 'in_progress',
            'live_url' => 'https://example.com/live',
            'github_url' => 'https://github.com/example/repo',
            'sort_order' => 7,
            'is_featured' => 1,
            'is_published' => 1,
            'technologies' => [$this->technology->id, $this->otherTechnology->id],
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.projects.store'), $payload)
            ->assertRedirect(route('admin.projects.index'));

        $project = Project::where('slug', 'new-project')->firstOrFail();

        $this->assertSame('Yangi loyiha', $project->title_uz);
        $this->assertSame('English description', $project->description_en);
        $this->assertSame('in_progress', $project->status);
        $this->assertSame('https://example.com/live', $project->live_url);
        $this->assertSame(7, $project->sort_order);
        $this->assertTrue($project->is_featured);
        $this->assertTrue($project->is_published);
        $this->assertCount(2, $project->technologies);
    }

    public function test_create_validation_rejects_invalid_fields(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.projects.store'), $this->payload([
                'slug' => 'Invalid Slug',
                'title_uz' => '',
                'title_en' => '',
                'status' => 'wrong',
                'live_url' => 'not-a-url',
                'sort_order' => -1,
                'technologies' => [99999],
            ]))
            ->assertSessionHasErrors([
                'slug',
                'title_uz',
                'title_en',
                'status',
                'live_url',
                'sort_order',
                'technologies.0',
            ]);
    }

    public function test_screenshot_upload_is_stored_with_type_and_size_limits(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.projects.store'), $this->payload([
                'slug' => 'with-screenshot',
                'screenshot' => UploadedFile::fake()->image('screen.png', 120, 120),
            ]))
            ->assertRedirect(route('admin.projects.index'));

        $project = Project::where('slug', 'with-screenshot')->firstOrFail();
        $image = $project->images()->firstOrFail();

        $this->assertSame('projects', dirname($image->path));
        Storage::disk('public')->assertExists($image->path);

        $this->actingAs($this->admin)
            ->post(route('admin.projects.store'), $this->payload([
                'slug' => 'bad-type',
                'screenshot' => UploadedFile::fake()->create('notes.txt', 10, 'text/plain'),
            ]))
            ->assertSessionHasErrors('screenshot');

        $this->actingAs($this->admin)
            ->post(route('admin.projects.store'), $this->payload([
                'slug' => 'too-big',
                'screenshot' => UploadedFile::fake()->create('huge.png', 2049, 'image/png'),
            ]))
            ->assertSessionHasErrors('screenshot');
    }

    public function test_admin_can_edit_and_update_project(): void
    {
        $project = $this->createProject([
            'slug' => 'editable-project',
            'title_en' => 'Editable Project',
            'is_featured' => true,
            'is_published' => true,
            'sort_order' => 1,
        ]);
        $project->technologies()->sync([$this->technology->id]);

        $this->actingAs($this->admin)
            ->get(route('admin.projects.edit', $project))
            ->assertOk()
            ->assertSee('Editable Project');

        $this->actingAs($this->admin)
            ->put(route('admin.projects.update', $project), $this->payload([
                'slug' => 'editable-project',
                'title_en' => 'Updated Project',
                'status' => 'production',
                'sort_order' => 12,
                'is_featured' => 0,
                'is_published' => 0,
                'technologies' => [$this->otherTechnology->id],
            ]))
            ->assertRedirect(route('admin.projects.index'));

        $project->refresh();

        $this->assertSame('Updated Project', $project->title_en);
        $this->assertSame(12, $project->sort_order);
        $this->assertFalse($project->is_featured);
        $this->assertFalse($project->is_published);
        $this->assertTrue($project->technologies->contains($this->otherTechnology));
        $this->assertFalse($project->technologies->contains($this->technology));
    }

    public function test_admin_can_delete_selected_screenshot(): void
    {
        $project = $this->createProject(['slug' => 'screenshot-delete']);

        $image = $project->images()->create([
            'path' => 'projects/old.png',
            'alt_uz' => 'Eski',
            'alt_en' => 'Old',
        ]);
        Storage::disk('public')->put('projects/old.png', 'fake-image');

        $this->actingAs($this->admin)
            ->put(route('admin.projects.update', $project), $this->payload([
                'slug' => 'screenshot-delete',
                'delete_screenshots' => [$image->id],
            ]))
            ->assertRedirect(route('admin.projects.index'));

        $this->assertDatabaseMissing('project_images', ['id' => $image->id]);
        Storage::disk('public')->assertMissing('projects/old.png');
    }

    public function test_admin_can_delete_project(): void
    {
        $project = $this->createProject(['slug' => 'delete-me']);
        $image = $project->images()->create([
            'path' => 'projects/delete-me.png',
            'alt_uz' => 'O‘chirish',
            'alt_en' => 'Delete',
        ]);
        Storage::disk('public')->put('projects/delete-me.png', 'fake-image');

        $this->actingAs($this->admin)
            ->delete(route('admin.projects.destroy', $project))
            ->assertRedirect(route('admin.projects.index'));

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
        $this->assertDatabaseMissing('project_images', ['id' => $image->id]);
        Storage::disk('public')->assertMissing('projects/delete-me.png');
    }

    public function test_draft_projects_are_hidden_from_public_pages(): void
    {
        $this->createProject([
            'slug' => 'public-project',
            'title_en' => 'Public Project',
            'is_published' => true,
        ]);
        $this->createProject([
            'slug' => 'draft-project',
            'title_en' => 'Draft Project',
            'is_published' => false,
        ]);

        $this->get('/en/projects')
            ->assertOk()
            ->assertSee('Public Project')
            ->assertDontSee('Draft Project');

        $this->get('/en/projects/draft-project')->assertNotFound();
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'slug' => 'default-project',
            'title_uz' => 'Vazifa loyihasi',
            'title_en' => 'Default Project',
            'summary_uz' => 'Uzbek summary',
            'summary_en' => 'English summary',
            'description_uz' => 'Uzbek description',
            'description_en' => 'English description',
            'status' => 'production',
            'live_url' => 'https://example.com',
            'github_url' => '',
            'sort_order' => 1,
            'is_featured' => 0,
            'is_published' => 1,
            'technologies' => [$this->technology->id],
        ], $overrides);
    }

    private function createProject(array $overrides = []): Project
    {
        return Project::create(array_merge([
            'slug' => 'created-project',
            'title_uz' => 'Yaratilgan loyiha',
            'title_en' => 'Created Project',
            'status' => 'production',
            'is_featured' => false,
            'is_published' => true,
            'sort_order' => 1,
        ], $overrides));
    }
}
