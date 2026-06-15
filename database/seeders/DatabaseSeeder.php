<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            RolePermissionSeeder::class,
            DocumentTemplateSeeder::class,
            DemoUserSeeder::class,
            BlogPostSeeder::class,
            ProductSeeder::class,
            TestimonialSeeder::class,
        ]);
    }
}
