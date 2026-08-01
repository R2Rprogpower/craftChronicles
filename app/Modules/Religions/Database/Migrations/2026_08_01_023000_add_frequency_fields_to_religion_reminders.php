<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('religion_reminders', function (Blueprint $table): void {
            $table->enum('schedule_preset', ['command', 'interval', 'daily', 'weekly', 'monthly', 'yearly', 'custom_cron'])
                ->default('command')
                ->after('frequency_mode');
            $table->unsignedInteger('interval_minutes')->nullable()->after('schedule_preset');
            $table->string('schedule_time', 5)->nullable()->after('interval_minutes');
            $table->enum('schedule_weekday', ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'])->nullable()->after('schedule_time');
            $table->unsignedTinyInteger('schedule_monthday')->nullable()->after('schedule_weekday');
            $table->unsignedTinyInteger('schedule_year_month')->nullable()->after('schedule_monthday');
            $table->unsignedTinyInteger('schedule_year_day')->nullable()->after('schedule_year_month');
        });
    }

    public function down(): void
    {
        Schema::table('religion_reminders', function (Blueprint $table): void {
            $table->dropColumn([
                'schedule_preset',
                'interval_minutes',
                'schedule_time',
                'schedule_weekday',
                'schedule_monthday',
                'schedule_year_month',
                'schedule_year_day',
            ]);
        });
    }
};
