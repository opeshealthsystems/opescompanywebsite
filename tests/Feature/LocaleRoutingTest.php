<?php

namespace Tests\Feature;

use Database\Seeders\ProductSeeder;
use Database\Seeders\TestimonialSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleRoutingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(ProductSeeder::class);
        $this->seed(TestimonialSeeder::class);
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
