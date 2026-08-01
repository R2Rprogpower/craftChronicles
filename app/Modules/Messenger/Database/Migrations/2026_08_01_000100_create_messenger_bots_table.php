<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('messenger_bots', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('driver', 32);
            $table->text('bot_token');
            $table->string('external_bot_id')->nullable();
            $table->string('username')->nullable();
            $table->boolean('is_active')->default(true);
            $table->jsonb('meta_json')->nullable();
            $table->timestamps();

            $table->index('driver');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messenger_bots');
    }
};
