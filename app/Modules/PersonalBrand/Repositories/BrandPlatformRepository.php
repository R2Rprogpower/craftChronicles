<?php

declare(strict_types=1);

namespace App\Modules\PersonalBrand\Repositories;

use App\Models\BrandProfile;
use App\Models\BrandProgressArea;
use App\Models\ContentItem;
use App\Models\PortfolioItem;
use App\Models\Product;
use App\Models\ServiceOffering;

class BrandPlatformRepository
{
    /** @return array<string, mixed> */
    public function portfolioData(): array
    {
        return [
            'profile' => BrandProfile::query()->where('active', true)->where('key', 'primary')->firstOrFail(),
            'portfolio' => PortfolioItem::query()->where('active', true)->orderByDesc('featured')->orderBy('sort_order')->get(),
            'services' => ServiceOffering::query()->where('active', true)->orderBy('sort_order')->get(),
            'products' => Product::query()->where('active', true)->orderByDesc('featured')->orderBy('sort_order')->get(),
            'content' => ContentItem::query()->where('active', true)->orderByDesc('featured')->orderBy('sort_order')->get(),
        ];
    }

    /** @return array<string, mixed> */
    public function progressData(): array
    {
        return [
            'profile' => BrandProfile::query()->where('active', true)->where('key', 'primary')->firstOrFail(),
            'areas' => BrandProgressArea::query()->with('milestones')->where('active', true)->orderBy('sort_order')->get(),
        ];
    }
}
