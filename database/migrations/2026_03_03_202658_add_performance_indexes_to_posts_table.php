<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Optimises the cursor-based "latest posts" query:
            // WHERE status='published' AND published_at <= now() AND id < ? ORDER BY id DESC
            $table->index(['status', 'id'], 'posts_status_id_idx');

            // Optimises the featured posts query:
            // WHERE status='published' AND is_featured=1 ORDER BY published_at DESC
            $table->index(['status', 'is_featured', 'published_at'], 'posts_status_featured_idx');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('posts_status_id_idx');
            $table->dropIndex('posts_status_featured_idx');
        });
    }
};
