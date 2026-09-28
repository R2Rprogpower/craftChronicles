<?php

declare(strict_types=1);

namespace App\Modules\AutomationLanding\Support;

final class OpenClawLocale
{
    public const DEFAULT = 'en';

    /** @var list<string> */
    public const SUPPORTED = ['en', 'uk', 'ru'];

    public static function resolve(mixed $locale): string
    {
        return is_string($locale) && in_array($locale, self::SUPPORTED, true)
            ? $locale
            : self::DEFAULT;
    }
}
