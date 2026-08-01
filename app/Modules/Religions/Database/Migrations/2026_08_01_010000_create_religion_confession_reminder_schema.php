<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('religions', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('confessions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('religion_id')->constrained('religions')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['religion_id', 'slug']);
        });

        Schema::create('rituals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('confession_id')->constrained('confessions')->cascadeOnDelete();
            $table->string('ritual_key');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['confession_id', 'ritual_key']);
        });

        Schema::create('reminder_types', function (Blueprint $table): void {
            $table->id();
            $table->string('type_key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_builtin')->default(true);
            $table->timestamps();
        });

        Schema::create('reminder_implementations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('reminder_type_id')->constrained('reminder_types')->cascadeOnDelete();
            $table->string('implementation_key');
            $table->enum('implementation_mode', ['manual', 'auto']);
            $table->string('handler_class')->nullable();
            $table->jsonb('config_json')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['reminder_type_id', 'implementation_key']);
        });

        Schema::create('religion_commands', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('confession_id')->constrained('confessions')->cascadeOnDelete();
            $table->string('command_key');
            $table->string('trigger')->comment('Example: /command1');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['confession_id', 'command_key']);
            $table->unique(['confession_id', 'trigger']);
        });

        Schema::create('religion_reminders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('confession_id')->constrained('confessions')->cascadeOnDelete();
            $table->foreignId('ritual_id')->nullable()->constrained('rituals')->nullOnDelete();
            $table->foreignId('reminder_type_id')->constrained('reminder_types')->cascadeOnDelete();
            $table->foreignId('implementation_id')->nullable()->constrained('reminder_implementations')->nullOnDelete();
            $table->foreignId('command_id')->nullable()->constrained('religion_commands')->nullOnDelete();
            $table->string('title');
            $table->longText('content_text')->nullable();
            $table->enum('text_format', ['plain', 'markdown', 'wysiwyg'])->default('plain');
            $table->enum('frequency_mode', ['manual', 'cron', 'interval', 'command'])->default('manual');
            $table->string('frequency_value')->nullable()->comment('Cron expr or interval syntax, nullable for command/manual');
            $table->string('command_trigger')->nullable()->comment('Optional direct command binding, ex: /chapter thisweek');
            $table->jsonb('meta_json')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['confession_id', 'is_active']);
            $table->index(['reminder_type_id', 'frequency_mode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('religion_reminders');
        Schema::dropIfExists('religion_commands');
        Schema::dropIfExists('reminder_implementations');
        Schema::dropIfExists('reminder_types');
        Schema::dropIfExists('rituals');
        Schema::dropIfExists('confessions');
        Schema::dropIfExists('religions');
    }
};
