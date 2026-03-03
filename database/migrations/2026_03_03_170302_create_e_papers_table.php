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
        Schema::create('e_papers', function (Blueprint $table) {
            $table->id();
            $table->date('edition_date')->index();
            $table->string('pdf_path');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('e_paper_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('e_paper_id')->constrained()->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('title');
            
            $table->unique(['e_paper_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_paper_translations');
        Schema::dropIfExists('e_papers');
    }
};
