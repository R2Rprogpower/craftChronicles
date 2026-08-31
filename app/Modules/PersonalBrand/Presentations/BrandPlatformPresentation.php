<?php

declare(strict_types=1);

namespace App\Modules\PersonalBrand\Presentations;

use App\Core\Abstracts\Presentation;

class BrandPlatformPresentation extends Presentation
{
    /** @return array<int|string, mixed> */
    public function present(mixed $data): array
    {
        $result = parent::present($data);
        unset($result['profile']['id'], $result['profile']['key'], $result['profile']['active'], $result['profile']['created_at'], $result['profile']['updated_at']);

        return $result;
    }
}
