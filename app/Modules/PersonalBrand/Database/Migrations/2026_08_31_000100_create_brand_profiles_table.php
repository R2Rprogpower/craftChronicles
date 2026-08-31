<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brand_profiles', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('headline');
            $table->text('bio');
            $table->string('location')->nullable();
            $table->string('availability')->nullable();
            $table->json('expertise')->nullable();
            $table->json('social_links')->nullable();
            $table->json('contact')->nullable();
            $table->json('faq')->nullable();
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brand_profiles');
    }
};
