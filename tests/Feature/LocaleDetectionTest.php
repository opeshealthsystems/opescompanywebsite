<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The bare root ("/") picks the locale from: a remembered-choice cookie first,
 * then the visitor's device/browser language (Accept-Language), then the default.
 * Visiting any /{locale} page records that locale in the cookie.
 */
class LocaleDetectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_bare_root_defaults_to_en_without_accept_language(): void
    {
        $this->get('/')->assertRedirect('/en');
    }

    public function test_bare_root_follows_browser_french(): void
    {
        $this->get('/', ['Accept-Language' => 'fr-FR,fr;q=0.9,en;q=0.8'])
            ->assertRedirect('/fr');
    }

    public function test_bare_root_follows_browser_english(): void
    {
        $this->get('/', ['Accept-Language' => 'en-US,en;q=0.9'])
            ->assertRedirect('/en');
    }

    public function test_remembered_choice_cookie_overrides_browser_language(): void
    {
        // Browser prefers French, but the visitor previously chose English.
        $this->withUnencryptedCookie('locale', 'en')
            ->get('/', ['Accept-Language' => 'fr-FR,fr;q=0.9'])
            ->assertRedirect('/en');
    }

    public function test_unsupported_cookie_is_ignored_and_falls_back_to_browser(): void
    {
        $this->withUnencryptedCookie('locale', 'de')
            ->get('/', ['Accept-Language' => 'fr-FR,fr'])
            ->assertRedirect('/fr');
    }

    public function test_visiting_a_locale_records_it_in_the_cookie(): void
    {
        // 'locale' is excluded from encryption, so assert the plaintext cookie.
        $this->get('/fr')->assertOk()->assertPlainCookie('locale', 'fr');
    }
}
