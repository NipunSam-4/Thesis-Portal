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
        Schema::create('comment_snippets', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->string('form_type')->default('all')->index(); // 'pts1', 'pts2', 'pts2_extension', 'all'
            $table->string('role')->default('section_officer')->index(); // 'section_officer', 'all', etc.
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_snippets');
    }
};
