<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('language_words', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('language_pack_id')->constrained('language_packs')->cascadeOnDelete();
            $table->string('word');
            $table->string('translation');
            $table->string('transcription');
            $table->text('phrases')->nullable();
            $table->jsonb('meta_json')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['language_pack_id', 'is_active']);
            $table->index(['word']);
            $table->unique(['language_pack_id', 'word', 'translation']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('language_words');
    }
};
