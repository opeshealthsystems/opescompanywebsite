<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_contains_only_public_routes(): void
    {
        $response = $this->get('/sitemap.xml')->assertOk();

        $response->assertSee('/en/products', false);
        $response->assertSee('/fr/contact', false);

        foreach (['/customer/', '/practitioner/', '/tester/', '/manager/', '/hr/', '/accountant/', '/support/', '/admin'] as $privatePath) {
            $response->assertDontSee($privatePath, false);
        }
    }
}
