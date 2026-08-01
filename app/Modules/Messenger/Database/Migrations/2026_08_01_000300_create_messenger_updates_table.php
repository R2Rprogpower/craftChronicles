<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('messenger_updates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('messenger_bot_id')->constrained('messenger_bots')->cascadeOnDelete();
            $table->string('driver', 32);
            $table->string('external_update_id')->nullable();
            $table->jsonb('payload_json');
            $table->timestamps();

            $table->index(['messenger_bot_id', 'driver']);
            $table->index('external_update_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messenger_updates');
    }
};
