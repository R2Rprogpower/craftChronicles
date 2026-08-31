<?php

declare(strict_types=1);

namespace App\Modules\PersonalBrand\Processors;

use App\Core\Abstracts\Processor;
use App\Modules\PersonalBrand\Services\BrandPlatformService;

class ProgressPageProcessor extends Processor
{
    public function __construct(private readonly BrandPlatformService $service) {}

    /** @return array<string, mixed> */
    public function execute(): array
    {
        return $this->service->progress();
    }
}
