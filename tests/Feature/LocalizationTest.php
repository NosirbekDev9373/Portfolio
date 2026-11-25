<?php

namespace Tests\Feature;

use Tests\TestCase;

class LocalizationTest extends TestCase
{
    public function test_supported_locales_render_the_home_page(): void
    {
        $this->get('/uz')
            ->assertOk()
            ->assertSee('Men PHP va Laravel bilan raqamli mahsulotlar yarataman.');

        $this->get('/en')
            ->assertOk()
            ->assertSee('I build digital products with PHP and Laravel.');
    }

    public function test_unsupported_locale_returns_not_found(): void
    {
        $this->get('/fr')->assertNotFound();
    }

    public function test_hreflang_links_are_rendered(): void
    {
        $this->get('/uz')
            ->assertSee('<link rel="alternate" hreflang="uz"', false)
            ->assertSee('<link rel="alternate" hreflang="en"', false)
            ->assertSee('<link rel="alternate" hreflang="x-default"', false);
    }

    public function test_language_switcher_links_to_the_other_locale(): void
    {
        $this->get('/uz')
            ->assertSee('href="'.url('/en').'"', false);

        $this->get('/en')
            ->assertSee('href="'.url('/uz').'"', false);
    }

    public function test_locale_is_remembered_in_the_session(): void
    {
        $this->get('/en')->assertSessionHas('locale', 'en');

        $this->withSession(['locale' => 'en'])
            ->get('/')
            ->assertRedirect('/en');
    }

    public function test_site_translation_files_have_matching_keys(): void
    {
        $english = require lang_path('en/site.php');
        $uzbek = require lang_path('uz/site.php');

        $this->assertSame($this->translationKeys($english), $this->translationKeys($uzbek));
    }

    private function translationKeys(array $translations, string $prefix = ''): array
    {
        $keys = [];

        foreach ($translations as $key => $value) {
            $fullKey = $prefix === '' ? (string) $key : $prefix.'.'.$key;

            if (is_array($value)) {
                $keys = array_merge($keys, $this->translationKeys($value, $fullKey));
            } else {
                $keys[] = $fullKey;
            }
        }

        sort($keys);

        return $keys;
    }
}
