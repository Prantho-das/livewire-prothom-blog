<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasIndex('category_post', 'cat_post_idx')) {
            Schema::table('category_post', function (Blueprint $table) {
                $table->index(['category_id', 'post_id'], 'cat_post_idx');
            });
        }

        if (! Schema::hasIndex('post_translations', 'post_locale_idx')) {
            Schema::table('post_translations', function (Blueprint $table) {
                $table->index(['post_id', 'locale'], 'post_locale_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasIndex('category_post', 'cat_post_idx')) {
            Schema::table('category_post', function (Blueprint $table) {
                $table->dropIndex('cat_post_idx');
            });
        }

        if (Schema::hasIndex('post_translations', 'post_locale_idx')) {
            Schema::table('post_translations', function (Blueprint $table) {
                $table->dropIndex('post_locale_idx');
            });
        }
    }
};
