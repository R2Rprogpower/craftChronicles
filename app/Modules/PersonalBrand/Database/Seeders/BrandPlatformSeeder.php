<?php

declare(strict_types=1);

namespace App\Modules\PersonalBrand\Database\Seeders;

use App\Modules\BrandProgress\Database\Seeders\BrandProgressSeeder;
use App\Modules\Content\Database\Seeders\ContentSeeder;
use App\Modules\Portfolio\Database\Seeders\PortfolioSeeder;
use App\Modules\Products\Database\Seeders\ProductsSeeder;
use App\Modules\Services\Database\Seeders\ServicesSeeder;
use Illuminate\Database\Seeder;

class BrandPlatformSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PersonalBrandSeeder::class,
            PortfolioSeeder::class,
            ServicesSeeder::class,
            ProductsSeeder::class,
            ContentSeeder::class,
            BrandProgressSeeder::class,
        ]);
    }
}
