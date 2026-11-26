<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SkillController extends Controller
{
    private const GROUPS = ['backend', 'frontend', 'database', 'tools', 'testing'];

    private const STATUSES = ['used', 'learning'];

    public function index(): View
    {
        $skillsByGroup = collect(self::GROUPS)
            ->mapWithKeys(fn (string $group): array => [
                $group => Skill::query()
                    ->where('group', $group)
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get(),
            ]);

        return view('admin.skills.index', compact('skillsByGroup'));
    }

    public function create(): View
    {
        return view('admin.skills.create', [
            'skill' => new Skill,
            'groups' => self::GROUPS,
            'statuses' => self::STATUSES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->validateSkill($request);

        Skill::create($request->only(['name', 'group', 'status', 'sort_order']));

        return redirect()->route('admin.skills.index')->with('status', 'Skill created.');
    }

    public function edit(Skill $skill): View
    {
        return view('admin.skills.edit', [
            'skill' => $skill,
            'groups' => self::GROUPS,
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(Request $request, Skill $skill): RedirectResponse
    {
        $this->validateSkill($request, $skill);

        $skill->update($request->only(['name', 'group', 'status', 'sort_order']));

        return redirect()->route('admin.skills.index')->with('status', 'Skill updated.');
    }

    public function destroy(Skill $skill): RedirectResponse
    {
        $skill->delete();

        return redirect()->route('admin.skills.index')->with('status', 'Skill deleted.');
    }

    private function validateSkill(Request $request, ?Skill $skill = null): void
    {
        $unique = Rule::unique('skills', 'name')
            ->where(fn ($query) => $query->where('group', $request->input('group')));

        if ($skill !== null) {
            $unique->ignore($skill->id);
        }

        $request->validate([
            'name' => ['required', 'string', 'min:1', 'max:80', $unique],
            'group' => ['required', Rule::in(self::GROUPS)],
            'status' => ['required', Rule::in(self::STATUSES)],
            'sort_order' => ['required', 'integer', 'min:0', 'max:10000'],
        ]);
    }
}
