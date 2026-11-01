<?php
namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;

class PublicEditorialSeeder extends Seeder
{
    public function run(): void
    {
        $articles = json_decode(file_get_contents(base_path('content/editorial/articles.json')), true, 512, JSON_THROW_ON_ERROR);
        foreach ($articles as $article) {
            BlogPost::firstOrCreate(['slug' => $article['slug']], array_merge($article, [
                'author' => 'OPES Health Systems', 'published' => true, 'published_at' => now(),
                'reading_time' => BlogPost::calculateReadingTime($article['body']),
            ]));
        }
    }
}
