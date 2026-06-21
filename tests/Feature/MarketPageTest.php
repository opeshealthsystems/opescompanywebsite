<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_markets_index_resolves(): void
    {
        $this->get('/en/markets')->assertOk()->assertSee('CEMAC');
    }

    public function test_all_five_markets_resolve(): void
    {
        foreach (['gabon', 'congo-brazzaville', 'chad', 'central-african-republic', 'equatorial-guinea'] as $slug) {
            $this->get("/en/markets/{$slug}")->assertOk();
        }
    }

    public function test_gabon_market_shows_cnam_payer(): void
    {
        $this->get('/en/markets/gabon')->assertOk()->assertSee('Gabon')->assertSee('CNAM');
    }

    public function test_chad_market_shows_offline_first(): void
    {
        $this->get('/en/markets/chad')->assertOk()->assertSee('Chad')->assertSee('Offline-first');
    }

    public function test_fr_market_resolves_and_localises(): void
    {
        $this->get('/fr/markets/equatorial-guinea')->assertOk()->assertSee('INSESO');
    }

    public function test_unknown_market_returns_404(): void
    {
        $this->get('/en/markets/kenya')->assertStatus(404);
    }

    /**
     * Guard: the new country pages must NOT affect the existing landing page.
     */
    public function test_home_page_still_renders(): void
    {
        $this->get('/en')->assertOk();
    }
}
