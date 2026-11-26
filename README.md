# PHP / Laravel full-stack dasturchining shaxsiy portfolio sayti.

Loyiha `Dark Editorial + Bento Grid` yo‘nalishida ishlangan; interfeys ikki tilda: `/uz` (standart) va `/en`. Sayt production’dagi shaxsiy loyihalarni case study shaklida namoyish etadi. Loyihalar va ularning matnlari admin panel orqali boshqariladi.

Ikkita production loyihasi ham shaxsiy loyiha hisoblanadi va professional ish tajribasi sifatida ko‘rsatilmaydi.

## Stack

- PHP 8.3+, Laravel 13
- Blade, Tailwind CSS 4 (`@tailwindcss/vite`), Vite
- PostgreSQL
- PHPUnit, Pint
- Laravel Breeze — faqat login/logout
- Production: Nginx + PHP-FPM, HTTPS

## Production loyihalar

| Loyiha | Live demo | Repository | Stack |
| --- | --- | --- | --- |
| Steak House | https://steakhouse.nweb.uz/ | Ochiq emas | PHP, Laravel, Blade, SQLite, Bootstrap, PHPUnit |
| Tozalash Web Sayt | https://work.nweb.uz/ | https://github.com/NosirbekDev9373/Laravel-for-testing-website | PHP, Laravel, Tailwind CSS, Vite, Sanctum, PHPUnit, PostgreSQL |

Steak House — ikki tilli (UZ/EN) restoran sayti: 23 ta taom, 6 toifa bo‘yicha filtr va pagination, jamoa, galereya, aloqa formasi hamda SEO. Aloqa formasi faqat validatsiya qiladi; ma’lumotni saqlamaydi va pochta yubormaydi.

Tozalash Web Sayt — ko‘p tilli (UZ/RU/EN) xizmatlar platformasi: 14 kategoriya, xizmat postlari, mijozlar fikrlari, blog, autentifikatsiya va admin panel. Post API hozircha izohga olingan.

## Lokal ishga tushirish

```bash
cd portfolio
composer install
cp .env.example .env
php artisan key:generate
```

`.env` faylida PostgreSQL ulanishini sozlang (`DB_CONNECTION`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`), keyin:

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

`php artisan db:seed` faqat lokal muhit uchun; production’da loyihalar avtomatik seed qilinmaydi.

Admin panel `/admin` manzilida. Foydalanuvchi yaratish:

```bash
php artisan app:create-admin admin@example.com
```

Parol yashirin so‘raladi va kamida 12 belgi bo‘lishi kerak.

## Testlar

```bash
php artisan test
./vendor/bin/pint
```

## Deploy

Production konfiguratsiyalari [deploy/](deploy/) papkasida:

- `deploy/nginx.conf` — Nginx vhost: HTTPS, gzip, statik asset cache.
- `deploy/php-fpm-pool.conf` — PHP-FPM pool va OPcache sozlamalari.
- `deploy/.env.production.example` — production `.env` namunasi.

Deploy chog‘ida domen, SSL yo‘li, PHP-FPM socket’i va barcha parollar o‘zgartirilishi kerak. To‘liq tartib:

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan key:generate
php artisan migrate --force
php artisan app:create-admin <email>
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
sudo systemctl reload php8.3-fpm
```

Ixtiyoriy, bir martalik (faqat boshlang‘ich loyihalarni yuklash uchun):

```bash
php artisan db:seed --class=ProjectSeeder --force
```

`php-fpm` reload qilinadi, chunki `php_admin_value[opcache.validate_timestamps] = 0` holatida yangi PHP kod jarayonlar qayta boshlanmaguncha ko‘rinmaydi.
