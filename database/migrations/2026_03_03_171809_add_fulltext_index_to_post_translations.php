<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite doesn't support FULLTEXT indexes in the same way MySQL does.
        // For 1M+ records on MySQL, we would use:
        // Schema::table('post_translations', function (Blueprint $table) {
        //     $table->fullText(['title', 'content']);
        // });
        
        // Since we are likely using MySQL/MariaDB for production, 
        // we'll use a conditional check or just define it if not SQLite.
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('post_translations', function (Blueprint $table) {
                $table->fullText(['title', 'content']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('post_translations', function (Blueprint $table) {
                $table->dropFullText(['title', 'content']);
            });
        }
    }
};
