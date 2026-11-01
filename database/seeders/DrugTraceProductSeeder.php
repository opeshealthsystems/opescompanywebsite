<?php
namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class DrugTraceProductSeeder extends Seeder
{
    public function run(): void
    {
        // Keep the existing 22-product catalogue when first populating an empty DB.
        if (!Product::exists()) {
            $this->call(ProductSeeder::class);
        }
        $data = config('products_drugtrace.opes-drugtrace');
        Product::firstOrCreate(['slug' => 'opes-drugtrace'], [
            'name' => $data['name'], 'name_fr' => $data['name'],
            'subtitle' => $data['subtitle'], 'subtitle_fr' => 'Sécurité du médicament et traçabilité pharmaceutique',
            'category' => 'specialist', 'icon' => $data['icon'], 'color' => $data['color'],
            'tagline' => $data['tagline'], 'is_active' => true, 'is_featured' => true, 'sort_order' => 40,
        ]);
    }
}
