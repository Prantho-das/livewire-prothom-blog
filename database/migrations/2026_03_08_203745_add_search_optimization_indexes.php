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
        Schema::table('posts', function (Blueprint $table) {
            // Index for faster published posts filtering and sorting
            if (! Schema::hasIndex('posts', 'posts_status_published_at_idx')) {
                $table->index(['status', 'published_at'], 'posts_status_published_at_idx');
            }
        });

        Schema::table('post_translations', function (Blueprint $table) {
            // Index for faster translation lookups by locale
            if (! Schema::hasIndex('post_translations', 'post_id_locale_idx')) {
                $table->index(['post_id', 'locale'], 'post_id_locale_idx');
            }
        });

        if (Schema::hasTable('category_post')) {
            Schema::table('category_post', function (Blueprint $table) {
                // Index for faster category-post lookups
                if (! Schema::hasIndex('category_post', 'category_id_post_id_idx')) {
                    $table->index(['category_id', 'post_id'], 'category_id_post_id_idx');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('posts_status_published_at_idx');
        });

        Schema::table('post_translations', function (Blueprint $table) {
            $table->dropIndex('post_id_locale_idx');
        });

        if (Schema::hasTable('category_post')) {
            Schema::table('category_post', function (Blueprint $table) {
                $table->dropIndex('category_id_post_id_idx');
            });
        }
    }
};
