<?php

declare(strict_types=1);

use App\Modules\Reminders\Services\HolidayCalendarResolver;
use App\Modules\Reminders\Services\RitualScheduleResolver;
use App\Modules\Reminders\Services\WeeklyChapterResolver;
use App\Modules\Telegram\Services\Handlers\ChapterThisWeekCommandHandler;
use App\Modules\Telegram\Services\Handlers\RitualExplainCommandHandler;

return [
    'map' => [
        'command-1' => HolidayCalendarResolver::class,
        'command-2' => WeeklyChapterResolver::class,
        'weekly-chapter' => ChapterThisWeekCommandHandler::class,
        'ritual-explain' => RitualExplainCommandHandler::class,
        'chapter' => ChapterThisWeekCommandHandler::class,
        'ritual' => RitualExplainCommandHandler::class,
        'ritual-schedule' => RitualScheduleResolver::class,
    ],
];
