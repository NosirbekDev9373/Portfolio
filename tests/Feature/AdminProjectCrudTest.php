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
            'email' => 'admin@example.com',
            'password' => 'secure-password-123',
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
            ->assertSee('name="robots" content="noindex"', false)
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
            'problem_uz' => 'Yangi muammo',
            'problem_en' => 'New project problem',
            'solution_uz' => 'Yangi yechim',
            'solution_en' => 'New project solution',
            'features' => [
                [
                    'title_uz' => 'Yangi imkoniyat',
                    'title_en' => 'New feature title',
                    'description_uz' => 'Yangi imkoniyat tavsifi',
                    'description_en' => 'New feature description',
                ],
            ],
            'contributions' => [
                [
                    'title_uz' => 'Yangi hissa',
                    'title_en' => 'New contribution title',
                    'description_uz' => 'Yangi hissa tavsifi',
                    'description_en' => 'New contribution description',
                ],
            ],
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
        $this->assertSame('New project problem', $project->problem_en);
        $this->assertSame('New project solution', $project->solution_en);
        $this->assertSame('New feature title', $project->features[0]['title_en']);
        $this->assertSame('New contribution description', $project->contributions[0]['description_en']);
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

    public function test_case_study_fields_are_validated(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.projects.store'), $this->payload([
                'slug' => 'partial-case-study',
                'features' => [
                    [
                        'title_uz' => 'Only a feature title',
                        'title_en' => '',
                        'description_uz' => '',
                        'description_en' => '',
                    ],
                ],
                'contributions' => [
                    [
                        'title_uz' => '',
                        'title_en' => 'Only a contribution title',
                        'description_uz' => '',
                        'description_en' => '',
                    ],
                ],
            ]))
            ->assertSessionHasErrors([
                'features.0.title_en',
                'features.0.description_uz',
                'features.0.description_en',
                'contributions.0.title_uz',
                'contributions.0.description_en',
            ])
            ->assertSessionDoesntHaveErrors([
                'features.0.title_uz',
                'contributions.0.title_en',
            ]);
    }

    public function test_empty_case_study_rows_are_not_stored(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.projects.store'), $this->payload([
                'slug' => 'empty-case-study',
                'features' => [
                    [
                        'title_uz' => '',
                        'title_en' => '',
                        'description_uz' => '',
                        'description_en' => '',
                    ],
                ],
                'contributions' => [
                    [
                        'title_uz' => '',
                        'title_en' => '',
                        'description_uz' => '',
                        'description_en' => '',
                    ],
                ],
            ]))
            ->assertRedirect(route('admin.projects.index'));

        $project = Project::where('slug', 'empty-case-study')->firstOrFail();

        $this->assertNull($project->features);
        $this->assertNull($project->contributions);
    }

    public function test_screenshot_uploads_are_stored_with_type_and_size_limits(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.projects.store'), $this->payload([
                'slug' => 'with-screenshot',
                'screenshots' => [UploadedFile::fake()->image('screen.png', 120, 120)],
            ]))
            ->assertRedirect(route('admin.projects.index'));

        $project = Project::where('slug', 'with-screenshot')->firstOrFail();
        $image = $project->images()->firstOrFail();

        $this->assertSame('projects', dirname($image->path));
        Storage::disk('public')->assertExists($image->path);
        $this->assertSame($project->title_uz, $image->alt_uz);
        $this->assertSame($project->title_en, $image->alt_en);
        $this->assertSame(1, $image->sort_order);

        $this->actingAs($this->admin)
            ->post(route('admin.projects.store'), $this->payload([
                'slug' => 'bad-type',
                'screenshots' => [UploadedFile::fake()->create('notes.txt', 10, 'text/plain')],
            ]))
            ->assertSessionHasErrors('screenshots.0');

        $this->actingAs($this->admin)
            ->post(route('admin.projects.store'), $this->payload([
                'slug' => 'too-big',
                'screenshots' => [UploadedFile::fake()->create('huge.png', 2049, 'image/png')],
            ]))
            ->assertSessionHasErrors('screenshots.0');
    }

    public function test_multiple_screenshot_uploads_are_appended_after_existing_images(): void
    {
        $project = $this->createProject(['slug' => 'append-screenshots']);

        $project->images()->create([
            'path' => 'projects/existing.png',
            'alt_uz' => 'Mavjud',
            'alt_en' => 'Existing',
            'sort_order' => 5,
        ]);
        Storage::disk('public')->put('projects/existing.png', 'fake-image');

        $this->actingAs($this->admin)
            ->put(route('admin.projects.update', $project), $this->payload([
                'slug' => 'append-screenshots',
                'title_uz' => $project->title_uz,
                'title_en' => $project->title_en,
                'screenshots' => [
                    UploadedFile::fake()->image('first.png', 120, 120),
                    UploadedFile::fake()->image('second.png', 120, 120),
                ],
            ]))
            ->assertRedirect(route('admin.projects.index'));

        $images = $project->images()->orderBy('sort_order')->get();

        $this->assertCount(3, $images);
        $this->assertSame([5, 6, 7], $images->pluck('sort_order')->all());
        $this->assertSame(
            [$project->title_uz, $project->title_uz],
            $images->slice(1)->values()->pluck('alt_uz')->all(),
        );
        $this->assertSame(
            [$project->title_en, $project->title_en],
            $images->slice(1)->values()->pluck('alt_en')->all(),
        );

        foreach ($images->slice(1)->pluck('path') as $path) {
            Storage::disk('public')->assertExists($path);
        }
    }

    public function test_project_cannot_have_more_than_ten_screenshots(): void
    {
        $project = $this->createProject(['slug' => 'limit-screenshots']);

        for ($sortOrder = 1; $sortOrder <= 9; $sortOrder++) {
            $project->images()->create([
                'path' => 'projects/existing-'.$sortOrder.'.png',
                'alt_uz' => 'Mavjud '.$sortOrder,
                'alt_en' => 'Existing '.$sortOrder,
                'sort_order' => $sortOrder,
            ]);
        }

        $this->actingAs($this->admin)
            ->put(route('admin.projects.update', $project), $this->payload([
                'slug' => 'limit-screenshots',
                'screenshots' => [
                    UploadedFile::fake()->image('first.png', 120, 120),
                    UploadedFile::fake()->image('second.png', 120, 120),
                ],
            ]))
            ->assertSessionHasErrors('screenshots');

        $this->assertSame(9, $project->images()->count());
    }

    public function test_store_rejects_more_than_ten_new_screenshots(): void
    {
        $files = [];

        for ($index = 0; $index < 11; $index++) {
            $files[] = UploadedFile::fake()->image('screen-'.$index.'.png', 20, 20);
        }

        $this->actingAs($this->admin)
            ->post(route('admin.projects.store'), $this->payload([
                'slug' => 'too-many-new',
                'screenshots' => $files,
            ]))
            ->assertSessionHasErrors('screenshots');

        $this->assertDatabaseMissing('projects', ['slug' => 'too-many-new']);
    }

    public function test_admin_can_update_screenshot_alt_text_and_sort_order(): void
    {
        $project = $this->createProject(['slug' => 'screenshot-meta']);

        $image = $project->images()->create([
            'path' => 'projects/meta.png',
            'alt_uz' => 'Eski alt',
            'alt_en' => 'Old alt',
            'sort_order' => 0,
        ]);
        Storage::disk('public')->put('projects/meta.png', 'fake-image');

        $this->actingAs($this->admin)
            ->put(route('admin.projects.update', $project), $this->payload([
                'slug' => 'screenshot-meta',
                'images' => [
                    $image->id => [
                        'alt_uz' => 'Yangi alt',
                        'alt_en' => 'New alt',
                        'sort_order' => 7,
                    ],
                ],
            ]))
            ->assertRedirect(route('admin.projects.index'));

        $image->refresh();

        $this->assertSame('Yangi alt', $image->alt_uz);
        $this->assertSame('New alt', $image->alt_en);
        $this->assertSame(7, $image->sort_order);
    }

    public function test_admin_cannot_update_another_projects_screenshot_metadata(): void
    {
        $project = $this->createProject(['slug' => 'first-project']);
        $otherProject = $this->createProject(['slug' => 'other-project']);

        $otherImage = $otherProject->images()->create([
            'path' => 'projects/other.png',
            'alt_uz' => 'Boshqa alt',
            'alt_en' => 'Other alt',
            'sort_order' => 2,
        ]);
        Storage::disk('public')->put('projects/other.png', 'fake-image');

        $this->actingAs($this->admin)
            ->put(route('admin.projects.update', $project), $this->payload([
                'slug' => 'first-project',
                'images' => [
                    $otherImage->id => [
                        'alt_uz' => 'Hujum',
                        'alt_en' => 'Attack',
                        'sort_order' => 99,
                    ],
                ],
            ]))
            ->assertRedirect(route('admin.projects.index'));

        $otherImage->refresh();

        $this->assertSame('Boshqa alt', $otherImage->alt_uz);
        $this->assertSame('Other alt', $otherImage->alt_en);
        $this->assertSame(2, $otherImage->sort_order);
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
                'problem_en' => 'Updated project problem',
                'features' => [
                    [
                        'title_uz' => 'Yangilangan imkoniyat',
                        'title_en' => 'Updated feature title',
                        'description_uz' => 'Yangilangan imkoniyat tavsifi',
                        'description_en' => 'Updated feature description',
                    ],
                ],
                'contributions' => [
                    [
                        'title_uz' => 'Yangilangan hissa',
                        'title_en' => 'Updated contribution title',
                        'description_uz' => 'Yangilangan hissa tavsifi',
                        'description_en' => 'Updated contribution description',
                    ],
                ],
                'status' => 'production',
                'sort_order' => 12,
                'is_featured' => 0,
                'is_published' => 0,
                'technologies' => [$this->otherTechnology->id],
            ]))
            ->assertRedirect(route('admin.projects.index'));

        $project->refresh();

        $this->assertSame('Updated Project', $project->title_en);
        $this->assertSame('Updated project problem', $project->problem_en);
        $this->assertSame('Updated feature title', $project->features[0]['title_en']);
        $this->assertSame('Updated contribution description', $project->contributions[0]['description_en']);
        $this->assertSame(12, $project->sort_order);
        $this->assertFalse($project->is_featured);
        $this->assertFalse($project->is_published);
        $this->assertTrue($project->technologies->contains($this->otherTechnology));
        $this->assertFalse($project->technologies->contains($this->technology));
    }

    public function test_admin_edit_form_uses_asset_urls_for_screenshots(): void
    {
        $project = $this->createProject(['slug' => 'asset-form']);

        $image = $project->images()->create([
            'path' => 'projects/asset.png',
            'alt_uz' => 'Asset UZ',
            'alt_en' => 'Asset EN',
            'sort_order' => 1,
        ]);
        Storage::disk('public')->put('projects/asset.png', 'fake-image');

        $this->actingAs($this->admin)
            ->get(route('admin.projects.edit', $project))
            ->assertOk()
            ->assertSee(asset('storage/projects/asset.png'), false)
            ->assertSee('name="screenshots[]"', false)
            ->assertSee('multiple', false)
            ->assertSee('name="images['.$image->id.'][alt_uz]"', false)
            ->assertDontSee('name="screenshot"', false)
            ->assertDontSee('name="screenshot_alt_uz"', false)
            ->assertDontSee('name="screenshot_sort_order"', false);
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
            'problem_uz' => 'Uzbek problem',
            'problem_en' => 'English problem',
            'solution_uz' => 'Uzbek solution',
            'solution_en' => 'English solution',
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
