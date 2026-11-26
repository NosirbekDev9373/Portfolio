<?php

namespace Tests\Feature;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSkillCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'secure-password-123',
        ]);
    }

    public function test_guest_is_redirected_from_admin_skills(): void
    {
        $this->get(route('admin.skills.index'))->assertRedirect('/login');
    }

    public function test_admin_index_lists_skills_by_group(): void
    {
        Skill::create(['name' => 'Laravel 13', 'group' => 'backend', 'sort_order' => 2]);
        Skill::create(['name' => 'JavaScript', 'group' => 'frontend', 'sort_order' => 1]);
        Skill::create(['name' => 'PHPUnit', 'group' => 'testing']);

        $html = $this->actingAs($this->admin)
            ->get(route('admin.skills.index'))
            ->assertOk()
            ->assertSeeText('Skills')
            ->assertSee('Laravel 13')
            ->assertSee('JavaScript')
            ->assertSee('PHPUnit')
            ->assertSee('Delete this skill?', false)
            ->getContent();

        $backend = strpos($html, 'data-skill-group="backend"');
        $frontend = strpos($html, 'data-skill-group="frontend"');
        $testing = strpos($html, 'data-skill-group="testing"');

        $this->assertNotFalse($backend);
        $this->assertNotFalse($frontend);
        $this->assertNotFalse($testing);
        $this->assertTrue($backend < $frontend);
        $this->assertTrue($frontend < $testing);
    }

    public function test_admin_can_create_skill(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.skills.store'), [
                'name' => 'PostgreSQL',
                'group' => 'database',
                'status' => 'used',
                'sort_order' => 5,
            ])
            ->assertRedirect(route('admin.skills.index'))
            ->assertSessionHas('status', 'Skill created.');

        $this->assertDatabaseHas('skills', [
            'name' => 'PostgreSQL',
            'group' => 'database',
            'status' => 'used',
            'sort_order' => 5,
        ]);
    }

    public function test_skill_validation_rejects_invalid_fields(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.skills.store'), [
                'name' => '',
                'group' => 'backend',
                'status' => 'used',
                'sort_order' => 0,
            ])
            ->assertSessionHasErrors('name');

        $this->actingAs($this->admin)
            ->post(route('admin.skills.store'), [
                'name' => str_repeat('a', 81),
                'group' => 'backend',
                'status' => 'used',
                'sort_order' => 0,
            ])
            ->assertSessionHasErrors('name');

        $this->actingAs($this->admin)
            ->post(route('admin.skills.store'), [
                'name' => 'Docker',
                'group' => 'unknown',
                'status' => 'used',
                'sort_order' => 0,
            ])
            ->assertSessionHasErrors('group');

        $this->actingAs($this->admin)
            ->post(route('admin.skills.store'), [
                'name' => 'Docker',
                'group' => 'tools',
                'status' => 'unknown',
                'sort_order' => 0,
            ])
            ->assertSessionHasErrors('status');

        $this->actingAs($this->admin)
            ->post(route('admin.skills.store'), [
                'name' => 'Docker',
                'group' => 'tools',
                'status' => 'used',
                'sort_order' => -1,
            ])
            ->assertSessionHasErrors('sort_order');
    }

    public function test_skill_name_is_unique_per_group(): void
    {
        Skill::create(['name' => 'PHP 8.3+', 'group' => 'backend']);

        $this->actingAs($this->admin)
            ->post(route('admin.skills.store'), [
                'name' => 'PHP 8.3+',
                'group' => 'backend',
                'status' => 'used',
                'sort_order' => 1,
            ])
            ->assertSessionHasErrors('name');

        $this->actingAs($this->admin)
            ->post(route('admin.skills.store'), [
                'name' => 'PHP 8.3+',
                'group' => 'frontend',
                'status' => 'used',
                'sort_order' => 1,
            ])
            ->assertRedirect(route('admin.skills.index'));

        $this->assertSame(2, Skill::where('name', 'PHP 8.3+')->count());
    }

    public function test_admin_can_edit_and_update_skill(): void
    {
        $skill = Skill::create([
            'name' => 'Vite',
            'group' => 'tools',
            'status' => 'used',
            'sort_order' => 3,
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.skills.edit', $skill))
            ->assertOk()
            ->assertSee('value="Vite" class="mt-1', false)
            ->assertSee('<option value="tools" selected>', false)
            ->assertSee('<option value="used" selected>', false)
            ->assertSee('value="3" class="mt-1', false);

        $this->actingAs($this->admin)
            ->put(route('admin.skills.update', $skill), [
                'name' => 'Vite',
                'group' => 'tools',
                'status' => 'learning',
                'sort_order' => 7,
            ])
            ->assertRedirect(route('admin.skills.index'))
            ->assertSessionHas('status', 'Skill updated.');

        $this->assertDatabaseHas('skills', [
            'id' => $skill->id,
            'name' => 'Vite',
            'group' => 'tools',
            'status' => 'learning',
            'sort_order' => 7,
        ]);
    }

    public function test_skill_forms_have_save_and_cancel_controls(): void
    {
        $saveButton = '<button type="submit" class="btn btn-primary">Save</button>';
        $cancelLink = '<a href="'.route('admin.skills.index').'" class="btn btn-secondary">Cancel</a>';

        $this->actingAs($this->admin)
            ->get(route('admin.skills.create'))
            ->assertOk()
            ->assertSee($saveButton, false)
            ->assertSee($cancelLink, false);

        $skill = Skill::create(['name' => 'Sanctum', 'group' => 'backend']);

        $this->actingAs($this->admin)
            ->get(route('admin.skills.edit', $skill))
            ->assertOk()
            ->assertSee($saveButton, false)
            ->assertSee($cancelLink, false);
    }

    public function test_admin_can_delete_skill(): void
    {
        $skill = Skill::create(['name' => 'Sanctum', 'group' => 'backend']);

        $this->actingAs($this->admin)
            ->delete(route('admin.skills.destroy', $skill))
            ->assertRedirect(route('admin.skills.index'))
            ->assertSessionHas('status', 'Skill deleted.');

        $this->assertDatabaseMissing('skills', ['id' => $skill->id]);
    }
}
