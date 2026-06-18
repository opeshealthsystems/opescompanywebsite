<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_blog_index_accepts_valid_category(): void
    {
        BlogPost::factory()->create(['published' => true, 'category' => 'tech']);
        $response = $this->get('/en/blog?category=tech');
        $response->assertStatus(200);
    }

    public function test_blog_index_rejects_invalid_category(): void
    {
        BlogPost::factory()->create(['published' => true, 'category' => 'tech']);
        $response = $this->get('/en/blog?category=nonexistent_hack');
        $response->assertStatus(422);
    }

    public function test_blog_index_rejects_overlong_search(): void
    {
        $response = $this->get('/en/blog?search=' . str_repeat('a', 256));
        $response->assertStatus(422);
    }

    public function test_blog_index_accepts_valid_search(): void
    {
        $response = $this->get('/en/blog?search=health');
        $response->assertStatus(200);
    }

    public function test_blog_index_loads_without_params(): void
    {
        $response = $this->get('/en/blog');
        $response->assertStatus(200);
    }
}
