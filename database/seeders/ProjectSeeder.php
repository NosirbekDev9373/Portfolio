<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\Technology;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $technologies = [
            'PHP' => 'backend',
            'Laravel' => 'backend',
            'Blade' => 'backend',
            'Sanctum' => 'backend',
            'SQLite' => 'database',
            'PostgreSQL' => 'database',
            'Bootstrap' => 'frontend',
            'Tailwind CSS' => 'frontend',
            'Vite' => 'tools',
            'PHPUnit' => 'tools',
        ];

        $technologyModels = [];

        foreach ($technologies as $name => $group) {
            $technologyModels[$name] = Technology::firstOrCreate(
                ['name' => $name],
                ['group' => $group],
            );
        }

        $projects = [
            [
                'slug' => 'steak-house',
                'title_uz' => 'Steak House',
                'title_en' => 'Steak House',
                'summary_uz' => "Shaxsiy loyiha: production'da ishlayotgan ikki tilli (UZ/EN) restoran sayti. Taomnoma, filtr va pagination, jamoa, galereya, aloqa formasi hamda SEO.",
                'summary_en' => 'Personal project in production: a bilingual (UZ/EN) restaurant website with a menu, filtering and pagination, team, gallery, contact form, and SEO.',
                'description_uz' => "Steak House — shaxsiy loyiha, production'da joylashtirilgan ikki tilli (UZ/EN) restoran sayti. 7 sahifadan iborat bo'lib, 23 ta taom 6 ta toifa bo'yicha filtrlanadi va pagination qo'llaniladi. Saytda jamoa, galereya va aloqa/bron formasi mavjud. Til SetLocale middleware va lang fayllari orqali almashtiriladi. Har bir sahifa uchun SEO (title, description, Open Graph, Twitter), Schema.org Restaurant, avtomatik sitemap.xml va robots.txt sozlangan. Aloqa formasi validatsiya va throttle:5,1 bilan himoyalangan; hozircha ma'lumotni saqlamaydi va pochta yubormaydi. Feature va Unit testlar sahifalar, forma, til, tarjima kalitlari va sitemapni tekshiradi.",
                'description_en' => 'Steak House is a personal project deployed to production: a bilingual (UZ/EN) restaurant website. It has 7 pages, with 23 dishes filterable by 6 categories and pagination. The site includes team, gallery, and contact/booking form sections. Language switching is handled by SetLocale middleware and lang files. Each page has SEO (title, description, Open Graph, Twitter), Schema.org Restaurant markup, an automatically generated sitemap.xml, and robots.txt. The contact form is protected by validation and throttle:5,1; it currently does not store data or send email. Feature and Unit tests cover pages, the form, language, translation keys, and the sitemap.',
                'status' => 'production',
                'live_url' => 'https://steakhouse.nweb.uz/',
                'github_url' => null,
                'is_featured' => true,
                'is_published' => true,
                'sort_order' => 1,
                'technologies' => ['PHP', 'Laravel', 'Blade', 'SQLite', 'Bootstrap', 'PHPUnit'],
            ],
            [
                'slug' => 'tozalash-web-sayt',
                'title_uz' => 'Tozalash Web Sayt',
                'title_en' => 'Cleaning Services Website',
                'summary_uz' => "Shaxsiy loyiha: production'da ishlayotgan ko'p tilli (UZ/RU/EN) xizmatlar platformasi. Kategoriyalar, narx va davomiylik bilan xizmat postlari, mijozlar fikrlari, blog, autentifikatsiya va admin panel.",
                'summary_en' => 'Personal project in production: a multilingual (UZ/RU/EN) services platform with categories, service posts including pricing and duration, testimonials, a blog, authentication, and an admin panel.',
                'description_uz' => "Tozalash Web Sayt — shaxsiy loyiha, production'da joylashtirilgan ko'p tilli (UZ/RU/EN) xizmatlar platformasi. Platformada 14 kategoriya, narx va davomiylik ko'rsatilgan xizmat postlari, mijozlar fikrlari va blog mavjud. Foydalanuvchilar ro'yxatdan o'tishi va tizimga kirishi mumkin. Rollar (admin, editor, blogger, user), PostPolicy va admin/staff middleware orqali ruxsatlar boshqariladi. Admin panelda post CRUD va rasm/galereya, kategoriya, teglar, foydalanuvchi rollari, izohlar va aloqa xabarlari boshqariladi. Loyihada Events/Listeners, Observer, Jobs/Queue, Notifications, Mail, console command, Form Requests va API Resources qo'llanilgan. Post API hozircha izohga olingan.",
                'description_en' => 'Tozalash Web Sayt is a personal project deployed to production: a multilingual (UZ/RU/EN) services platform. It includes 14 categories, service posts with pricing and duration, customer testimonials, and a blog. Users can register and log in. Permissions are managed through roles (admin, editor, blogger, user), PostPolicy, and admin/staff middleware. The admin panel manages post CRUD with images/gallery, categories, tags, user roles, comments, and contact messages. The project uses Events/Listeners, Observers, Jobs/Queue, Notifications, Mail, a console command, Form Requests, and API Resources. The post API is currently commented out.',
                'status' => 'production',
                'live_url' => 'https://work.nweb.uz/',
                'github_url' => 'https://github.com/NosirbekDev9373/Laravel-for-testing-website',
                'is_featured' => true,
                'is_published' => true,
                'sort_order' => 2,
                'technologies' => ['PHP', 'Laravel', 'Tailwind CSS', 'Vite', 'Sanctum', 'PHPUnit', 'PostgreSQL'],
            ],
        ];

        foreach ($projects as $data) {
            $technologyNames = $data['technologies'];
            unset($data['technologies']);

            $project = Project::firstOrCreate(
                ['slug' => $data['slug']],
                $data,
            );

            if (! $project->wasRecentlyCreated) {
                continue;
            }

            $project->technologies()->sync(
                array_map(
                    fn (string $name): int => $technologyModels[$name]->id,
                    $technologyNames,
                ),
            );

            ProjectImage::firstOrCreate(
                ['project_id' => $project->id, 'path' => 'projects/placeholder.png'],
                [
                    'alt_uz' => $project->title_uz.' — placeholder screenshot',
                    'alt_en' => $project->title_en.' — placeholder screenshot',
                    'sort_order' => 0,
                ],
            );
        }
    }
}
