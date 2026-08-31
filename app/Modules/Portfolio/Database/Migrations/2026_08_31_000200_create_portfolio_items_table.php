<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_items', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->index();
            $table->string('status')->index();
            $table->text('short_description');
            $table->longText('description')->nullable();
            $table->json('technologies')->nullable();
            $table->json('links')->nullable();
            $table->string('timeline')->nullable();
            $table->json('achievements')->nullable();
            $table->json('metrics')->nullable();
            $table->json('media')->nullable();
            $table->boolean('featured')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_items');
    }
};
