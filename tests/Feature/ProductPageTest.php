<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProductPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_opescare_detail_page_resolves(): void
    {
        $response = $this->get('/en/products/opescare');
        $response->assertStatus(200);
    }

    public function test_opes_emr_detail_page_resolves(): void
    {
        $response = $this->get('/en/products/opes-emr');
        $response->assertStatus(200);
    }

    public function test_unknown_slug_returns_404(): void
    {
        $this->get('/en/products/nonexistent-product')->assertStatus(404);
    }

    public function test_product_page_shows_product_name(): void
    {
        $this->get('/en/products/opes-triage')
            ->assertOk()
            ->assertSee('Opes Triage')
            ->assertSee('Standalone');
    }

    public function test_fr_product_page_resolves(): void
    {
        $this->get('/fr/products/pharmis')->assertOk();
    }
}
