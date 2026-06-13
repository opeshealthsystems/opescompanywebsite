<?php

namespace Tests\Feature;

use Tests\TestCase;

class LocaleRoutingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_root_redirects_to_default_locale(): void
    {
        $this->get('/')->assertRedirect('/en');
    }

    public function test_english_homepage_loads(): void
    {
        $this->get('/en')->assertOk();
    }

    public function test_french_homepage_loads(): void
    {
        $this->get('/fr')->assertOk();
    }

    public function test_unsupported_locale_returns_404(): void
    {
        $this->get('/es')->assertNotFound();
    }

    public function test_locale_is_applied_to_the_app(): void
    {
        $this->get('/fr');
        $this->assertSame('fr', app()->getLocale());
    }
}
