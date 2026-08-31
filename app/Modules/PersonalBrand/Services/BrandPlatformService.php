<?php

declare(strict_types=1);

namespace App\Modules\PersonalBrand\Services;

use App\Modules\PersonalBrand\Repositories\BrandPlatformRepository;

class BrandPlatformService
{
    public function __construct(private readonly BrandPlatformRepository $repository) {}

    /** @return array<string, mixed> */
    public function portfolio(): array
    {
        return $this->repository->portfolioData();
    }

    /** @return array<string, mixed> */
    public function progress(): array
    {
        return $this->repository->progressData();
    }
}
