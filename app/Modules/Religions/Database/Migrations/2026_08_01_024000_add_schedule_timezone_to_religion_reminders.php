<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('religion_reminders', function (Blueprint $table): void {
            $table->string('schedule_timezone', 64)
                ->default('Europe/Kyiv')
                ->after('schedule_time');
        });

        DB::table('religion_reminders')
            ->whereNull('schedule_timezone')
            ->update(['schedule_timezone' => 'Europe/Kyiv']);
    }

    public function down(): void
    {
        Schema::table('religion_reminders', function (Blueprint $table): void {
            $table->dropColumn('schedule_timezone');
        });
    }
};
