<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brand_progress_areas', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('status')->index();
            $table->unsignedTinyInteger('progress')->default(0);
            $table->text('summary')->nullable();
            $table->json('goals')->nullable();
            $table->json('tasks')->nullable();
            $table->json('notes')->nullable();
            $table->json('next_steps')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });
        Schema::create('progress_milestones', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('brand_progress_area_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->index();
            $table->date('target_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress_milestones');
        Schema::dropIfExists('brand_progress_areas');
    }
};
