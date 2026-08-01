<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messenger_users', function (Blueprint $table): void {
            $table->id();
            $table->string('driver', 32);
            $table->string('external_user_id');
            $table->string('username')->nullable();
            $table->string('display_name')->nullable();
            $table->string('language_code', 16)->nullable();
            $table->jsonb('meta_json')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['driver', 'external_user_id'], 'messenger_users_driver_external_unique');
            $table->index(['driver', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messenger_users');
    }
};
