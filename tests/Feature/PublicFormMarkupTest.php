<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicFormMarkupTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_contact_form_has_html5_validation_matching_server_rules(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertSee('name="name" required maxlength="100"', false)
            ->assertSee('name="email" required maxlength="150"', false);
    }

    public function test_contact_page_form_has_html5_validation_matching_server_rules(): void
    {
        $this->get('/en/contact')
            ->assertOk()
            ->assertSee('name="name"', false)
            ->assertSee('required maxlength="100"', false)
            ->assertSee('name="email"', false)
            ->assertSee('required maxlength="150"', false)
            ->assertSee('name="phone"', false)
            ->assertSee('maxlength="30"', false)
            ->assertSee('name="products"', false)
            ->assertSee('maxlength="255"', false)
            ->assertSee('name="message"', false)
            ->assertSee('maxlength="2000"', false);
    }
}
