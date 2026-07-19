<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chess_profiles', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name', 30);
            $table->timestamps();
        });

        Schema::create('chess_games', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('white_profile_id')->nullable()->constrained('chess_profiles')->nullOnDelete();
            $table->foreignUuid('black_profile_id')->nullable()->constrained('chess_profiles')->nullOnDelete();
            $table->string('fen');
            $table->string('status', 20)->default('waiting');
            $table->string('result', 20)->nullable();
            $table->json('moves')->default('[]');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chess_games');
        Schema::dropIfExists('chess_profiles');
    }
};
