<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('confessions', function (Blueprint $table): void {
            $table->foreignId('language_pack_id')
                ->nullable()
                ->after('welcome_message')
                ->constrained('language_packs')
                ->nullOnDelete();
        });

        Schema::table('religion_reminders', function (Blueprint $table): void {
            $table->foreignId('language_pack_id')
                ->nullable()
                ->after('command_id')
                ->constrained('language_packs')
                ->nullOnDelete();
            $table->boolean('word_mode')
                ->default(false)
                ->after('language_pack_id');
        });
    }

    public function down(): void
    {
        Schema::table('religion_reminders', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('language_pack_id');
            $table->dropColumn('word_mode');
        });

        Schema::table('confessions', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('language_pack_id');
        });
    }
};
