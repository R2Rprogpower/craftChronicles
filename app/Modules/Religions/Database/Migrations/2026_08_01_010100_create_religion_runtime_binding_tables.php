<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_group_religion_preferences', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('messenger_user_id')->constrained('messenger_users')->cascadeOnDelete();
            $table->foreignId('messenger_group_link_id')->constrained('messenger_group_links')->cascadeOnDelete();
            $table->foreignId('confession_id')->constrained('confessions')->cascadeOnDelete();
            $table->foreignId('language_pack_id')->nullable()->constrained('language_packs')->nullOnDelete();
            $table->boolean('ritual_opt_in')->default(true);
            $table->boolean('is_active')->default(true);
            $table->jsonb('meta_json')->nullable();
            $table->timestamps();

            $table->unique(['messenger_user_id', 'messenger_group_link_id'], 'user_group_religion_pref_unique');
            $table->index(['confession_id', 'is_active']);
        });

        Schema::create('reminder_delivery_targets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('religion_reminder_id')->constrained('religion_reminders')->cascadeOnDelete();
            $table->enum('target_type', ['group', 'dm', 'channel']);
            $table->foreignId('messenger_group_link_id')->nullable()->constrained('messenger_group_links')->nullOnDelete();
            $table->foreignId('messenger_user_id')->nullable()->constrained('messenger_users')->nullOnDelete();
            $table->string('channel_key')->nullable();
            $table->boolean('is_active')->default(true);
            $table->jsonb('meta_json')->nullable();
            $table->timestamps();

            $table->index(['religion_reminder_id', 'target_type']);
            $table->index(['messenger_group_link_id', 'messenger_user_id'], 'reminder_target_group_user_idx');
        });

        Schema::create('reminder_runs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('religion_reminder_id')->constrained('religion_reminders')->cascadeOnDelete();
            $table->foreignId('reminder_delivery_target_id')->nullable()->constrained('reminder_delivery_targets')->nullOnDelete();
            $table->timestampTz('next_run_at_utc')->nullable();
            $table->timestampTz('scheduled_for_utc');
            $table->timestampTz('sent_at')->nullable();
            $table->enum('status', ['pending', 'queued', 'sent', 'failed', 'skipped', 'canceled'])->default('pending');
            $table->string('idempotency_key')->unique();
            $table->unsignedInteger('attempt_count')->default(0);
            $table->text('last_error')->nullable();
            $table->jsonb('payload_json')->nullable();
            $table->timestamps();

            $table->index(['scheduled_for_utc', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminder_runs');
        Schema::dropIfExists('reminder_delivery_targets');
        Schema::dropIfExists('user_group_religion_preferences');
    }
};
