<?php

declare(strict_types=1);

namespace App\Modules\Products\Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Developer English Lab', 'slug' => 'developer-english-lab', 'stage' => 'idea', 'description' => 'A practice system for developers who need confident English in real engineering work.', 'links' => [], 'roadmap' => ['Interview target users', 'Validate the first practice format', 'Run a small pilot'], 'featured' => true, 'sort_order' => 10],
            ['name' => 'Personal Brand OS', 'slug' => 'personal-brand-os', 'stage' => 'development', 'description' => 'The platform behind this site: portfolio, content planning, product experiments, progress, and sales in one modular system.', 'links' => [], 'roadmap' => ['Public foundation', 'Editorial workflow', 'Private operating dashboard'], 'featured' => true, 'sort_order' => 20],
        ];
        foreach ($products as $product) {
            Product::query()->updateOrCreate(['slug' => $product['slug']], $product + ['active' => true]);
        }
    }
}
