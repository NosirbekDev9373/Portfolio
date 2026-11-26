<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * @var array<int, array{group: string, name: string, status: string}>
     */
    private const SKILLS = [
        ['group' => 'backend', 'name' => 'PHP 8.3+', 'status' => 'used'],
        ['group' => 'backend', 'name' => 'Laravel 13', 'status' => 'used'],
        ['group' => 'backend', 'name' => 'OOP & MVC', 'status' => 'used'],
        ['group' => 'backend', 'name' => 'REST API', 'status' => 'used'],
        ['group' => 'backend', 'name' => 'Authentication & Authorization', 'status' => 'used'],
        ['group' => 'backend', 'name' => 'CRUD operations', 'status' => 'used'],
        ['group' => 'backend', 'name' => 'Middleware', 'status' => 'used'],
        ['group' => 'backend', 'name' => 'Policies', 'status' => 'used'],
        ['group' => 'backend', 'name' => 'Sanctum', 'status' => 'used'],
        ['group' => 'frontend', 'name' => 'HTML5', 'status' => 'used'],
        ['group' => 'frontend', 'name' => 'CSS3', 'status' => 'used'],
        ['group' => 'frontend', 'name' => 'JavaScript', 'status' => 'used'],
        ['group' => 'frontend', 'name' => 'Blade', 'status' => 'used'],
        ['group' => 'frontend', 'name' => 'Tailwind CSS', 'status' => 'used'],
        ['group' => 'frontend', 'name' => 'Responsive Web Design', 'status' => 'used'],
        ['group' => 'database', 'name' => 'PostgreSQL', 'status' => 'used'],
        ['group' => 'database', 'name' => 'MySQL', 'status' => 'used'],
        ['group' => 'database', 'name' => 'SQL', 'status' => 'used'],
        ['group' => 'database', 'name' => 'Database design & relationships', 'status' => 'used'],
        ['group' => 'database', 'name' => 'Migrations', 'status' => 'used'],
        ['group' => 'database', 'name' => 'Eloquent ORM', 'status' => 'used'],
        ['group' => 'tools', 'name' => 'Git', 'status' => 'used'],
        ['group' => 'tools', 'name' => 'GitHub', 'status' => 'used'],
        ['group' => 'tools', 'name' => 'Linux', 'status' => 'used'],
        ['group' => 'tools', 'name' => 'Nginx', 'status' => 'used'],
        ['group' => 'tools', 'name' => 'Docker', 'status' => 'used'],
        ['group' => 'tools', 'name' => 'GitHub Actions', 'status' => 'used'],
        ['group' => 'tools', 'name' => 'CI/CD', 'status' => 'used'],
        ['group' => 'tools', 'name' => 'Production deployment', 'status' => 'used'],
        ['group' => 'testing', 'name' => 'PHPUnit', 'status' => 'used'],
        ['group' => 'testing', 'name' => 'Feature & Unit testing', 'status' => 'used'],
    ];

    public function run(): void
    {
        $sortOrders = [];

        foreach (self::SKILLS as $skill) {
            $group = $skill['group'];
            $sortOrders[$group] = ($sortOrders[$group] ?? 0) + 1;

            Skill::firstOrCreate(
                [
                    'name' => $skill['name'],
                    'group' => $group,
                ],
                [
                    'status' => $skill['status'],
                    'sort_order' => $sortOrders[$group],
                ],
            );
        }
    }
}
