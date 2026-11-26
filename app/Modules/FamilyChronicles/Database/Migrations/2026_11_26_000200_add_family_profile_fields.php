<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('family_people', function (Blueprint $table): void {
            $table->date('birth_date')->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('birth_place', 160)->nullable();
            $table->string('residence', 160)->nullable();
            $table->string('occupation', 160)->nullable();
            $table->text('profile_notes')->nullable();
            $table->boolean('relatives_complete')->default(false);
        });

        Schema::create('family_relatives', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('family_person_id')->constrained('family_people')->cascadeOnDelete();
            $table->string('relationship', 40);
            $table->string('name', 120)->nullable();
            $table->unsignedSmallInteger('birth_year')->nullable();
            $table->timestamps();
            $table->index(['family_person_id', 'relationship']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_relatives');

        Schema::table('family_people', function (Blueprint $table): void {
            $table->dropColumn([
                'birth_date',
                'gender',
                'birth_place',
                'residence',
                'occupation',
                'profile_notes',
                'relatives_complete',
            ]);
        });
    }
};
