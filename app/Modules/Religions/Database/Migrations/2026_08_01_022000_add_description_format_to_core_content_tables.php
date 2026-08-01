<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('religions', function (Blueprint $table): void {
            $table->enum('description_format', ['plain', 'markdown', 'wysiwyg'])
                ->default('plain')
                ->after('description');
        });

        Schema::table('confessions', function (Blueprint $table): void {
            $table->enum('description_format', ['plain', 'markdown', 'wysiwyg'])
                ->default('plain')
                ->after('description');
        });

        Schema::table('rituals', function (Blueprint $table): void {
            $table->enum('description_format', ['plain', 'markdown', 'wysiwyg'])
                ->default('plain')
                ->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('rituals', function (Blueprint $table): void {
            $table->dropColumn('description_format');
        });

        Schema::table('confessions', function (Blueprint $table): void {
            $table->dropColumn('description_format');
        });

        Schema::table('religions', function (Blueprint $table): void {
            $table->dropColumn('description_format');
        });
    }
};
