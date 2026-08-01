<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('messenger_group_links', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('messenger_bot_id')->constrained('messenger_bots')->cascadeOnDelete();
            $table->string('external_chat_id');
            $table->string('title')->nullable();
            $table->boolean('is_active')->default(true);
            $table->jsonb('meta_json')->nullable();
            $table->timestamps();

            $table->unique(['messenger_bot_id', 'external_chat_id'], 'messenger_group_link_unique');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messenger_group_links');
    }
};
