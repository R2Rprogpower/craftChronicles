<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gratitude_variants', function (Blueprint $table): void {
            $table->id();
            $table->string('title', 120);
            $table->string('language', 2)->index();
            $table->text('body');
            $table->json('tags');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        $variants = json_decode(
            (string) file_get_contents(resource_path('content/gratitude-variants.json')),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        $now = now();

        DB::table('gratitude_variants')->insert(array_map(
            static fn (array $variant): array => [
                ...$variant,
                'tags' => json_encode($variant['tags'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            $variants,
        ));
    }

    public function down(): void
    {
        Schema::dropIfExists('gratitude_variants');
    }
};
