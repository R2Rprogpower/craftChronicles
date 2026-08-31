<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('contact')->nullable();
            $table->string('company')->nullable();
            $table->foreignId('service_offering_id')->nullable()->constrained()->nullOnDelete();
            $table->string('budget')->nullable();
            $table->text('message');
            $table->string('status')->default('new')->index();
            $table->string('source')->default('portfolio');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
