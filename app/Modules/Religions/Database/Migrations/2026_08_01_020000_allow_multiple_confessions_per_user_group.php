<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_group_religion_preferences', function (Blueprint $table): void {
            $table->dropUnique('user_group_religion_pref_unique');
            $table->unique(
                ['messenger_user_id', 'messenger_group_link_id', 'confession_id'],
                'user_group_religion_pref_user_group_confession_unique'
            );
            $table->index(
                ['messenger_user_id', 'messenger_group_link_id', 'is_active'],
                'user_group_religion_pref_user_group_active_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('user_group_religion_preferences', function (Blueprint $table): void {
            $table->dropIndex('user_group_religion_pref_user_group_active_idx');
            $table->dropUnique('user_group_religion_pref_user_group_confession_unique');
            $table->unique(['messenger_user_id', 'messenger_group_link_id'], 'user_group_religion_pref_unique');
        });
    }
};
