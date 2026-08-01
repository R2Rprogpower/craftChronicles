<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('confessions', function (Blueprint $table): void {
            $table->text('welcome_message')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('confessions', function (Blueprint $table): void {
            $table->dropColumn('welcome_message');
        });
    }
};
