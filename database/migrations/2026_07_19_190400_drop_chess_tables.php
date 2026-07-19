<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('chess_games');
        Schema::dropIfExists('chess_profiles');
    }

    public function down(): void
    {
        // Chess was intentionally removed; rollback does not restore its data.
    }
};
