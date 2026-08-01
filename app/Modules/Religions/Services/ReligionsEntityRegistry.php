<?php

declare(strict_types=1);

namespace App\Modules\Religions\Services;

use App\Modules\Religions\Models\Confession;
use App\Modules\Religions\Models\LanguagePack;
use App\Modules\Religions\Models\LanguageWord;
use App\Modules\Religions\Models\Religion;
use App\Modules\Religions\Models\ReligionCommand;
use App\Modules\Religions\Models\ReligionReminder;
use App\Modules\Religions\Models\ReminderDeliveryTarget;
use App\Modules\Religions\Models\ReminderImplementation;
use App\Modules\Religions\Models\ReminderRun;
use App\Modules\Religions\Models\ReminderType;
use App\Modules\Religions\Models\Ritual;
use App\Modules\Religions\Models\UserGroupReligionPreference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ReligionsEntityRegistry
{
    /**
     * @return array<string, class-string<Model>>
     */
    public function modelMap(): array
    {
        return [
            'religions' => Religion::class,
            'confessions' => Confession::class,
            'rituals' => Ritual::class,
            'reminder_types' => ReminderType::class,
            'reminder_implementations' => ReminderImplementation::class,
            'religion_commands' => ReligionCommand::class,
            'religion_reminders' => ReligionReminder::class,
            'language_packs' => LanguagePack::class,
            'language_words' => LanguageWord::class,
            'user_group_religion_preferences' => UserGroupReligionPreference::class,
            'reminder_delivery_targets' => ReminderDeliveryTarget::class,
            'reminder_runs' => ReminderRun::class,
        ];
    }

    /**
     * @return array<int, string>
     */
    public function entities(): array
    {
        return array_keys($this->modelMap());
    }

    /**
     * @return array<string, string>
     */
    public function entityLabels(): array
    {
        $labels = [];

        foreach ($this->entities() as $entity) {
            $labels[$entity] = Str::headline(str_replace('_', ' ', $entity));
        }

        return $labels;
    }

    /**
     * @return class-string<Model>
     */
    public function resolveModel(string $entity): string
    {
        $map = $this->modelMap();

        if (! isset($map[$entity])) {
            throw new InvalidArgumentException("Unsupported entity '{$entity}'.");
        }

        return $map[$entity];
    }
}
