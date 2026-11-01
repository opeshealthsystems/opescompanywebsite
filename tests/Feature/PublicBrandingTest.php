<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicBrandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_unseeded_catalogue_has_resolvable_cards_and_matching_home_count(): void
    {
        $products = Product::publicCatalogue();
        $this->assertNotEmpty($products);
        $this->get('/en')->assertOk()->assertSee($products->count().' Systems. One Ecosystem.');
        foreach ($products as $product) {
            $this->get('/en/products/'.$product->slug)->assertOk();
        }
        $this->get('/fr/products')->assertOk()->assertSee('produits au catalogue');
    }

    public function test_disabled_products_are_not_restored_by_catalogue_fallback(): void
    {
        Product::create(['slug' => 'opescare', 'name' => 'OPESCare', 'category' => 'core', 'is_active' => false]);
        $this->assertCount(0, Product::publicCatalogue());
    }

    public function test_contact_fields_have_linked_labels_in_both_languages(): void
    {
        foreach (['en', 'fr'] as $locale) {
            $response = $this->get('/'.$locale.'/contact')->assertOk();
            foreach (['name', 'email', 'phone', 'facility', 'products', 'message'] as $field) {
                $response->assertSee('for="contact-'.$field.'"', false)
                    ->assertSee('id="contact-'.$field.'"', false);
            }
        }
    }

    public function test_contact_validation_and_recorded_success_in_each_language(): void
    {
        \Spatie\Permission\Models\Role::findOrCreate('admin', 'web');
        config(['mail.admin_email' => null]);
        foreach (['en', 'fr'] as $locale) {
            $url = '/'.$locale.'/contact';
            $this->from($url)->post($url, ['name' => '', 'email' => 'invalid'])
                ->assertSessionHasErrors(['name', 'email']);
            $this->from($url)->post($url, ['name' => 'Brand QA', 'email' => $locale.'@example.test'])
                ->assertRedirect($url)->assertSessionHas('success', fn ($text) => str_contains($text, $locale === 'fr' ? 'enregistrée' : 'recorded'));
            $this->assertDatabaseHas('leads', ['email' => $locale.'@example.test', 'locale' => $locale]);
        }
    }
}
