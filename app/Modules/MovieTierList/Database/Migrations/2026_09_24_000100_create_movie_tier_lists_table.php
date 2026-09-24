<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movie_tier_lists', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->json('payload');
            $table->unsignedBigInteger('revision')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movie_tier_lists');
    }
};
