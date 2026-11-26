<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('family_people', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->unsignedSmallInteger('birth_year')->nullable();
            $table->string('relationship', 80)->nullable();
            $table->string('locale', 5)->default('ru');
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('biography_answers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('family_person_id')->constrained('family_people')->cascadeOnDelete();
            $table->string('question_key', 120);
            $table->string('status', 20)->default('draft');
            $table->longText('text')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();
            $table->unique(['family_person_id', 'question_key']);
            $table->index(['user_id', 'status']);
        });

        Schema::create('biography_attachments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('biography_answer_id')->constrained('biography_answers')->cascadeOnDelete();
            $table->string('original_name', 255);
            $table->string('path');
            $table->string('mime_type', 120);
            $table->unsignedBigInteger('size');
            $table->string('caption', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('biography_daily_questions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('family_person_id')->constrained('family_people')->cascadeOnDelete();
            $table->date('question_date');
            $table->string('question_key', 120);
            $table->timestamps();
            $table->unique(['family_person_id', 'question_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biography_daily_questions');
        Schema::dropIfExists('biography_attachments');
        Schema::dropIfExists('biography_answers');
        Schema::dropIfExists('family_people');
    }
};
