<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Run the migrations.
    public function up(): void
    {
        Schema::create('pts6_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thesis_id')->constrained('theses')->cascadeOnDelete();
            $table->string('current_stage')->default('main_supervisor');
            $table->enum('status', ['pending', 'in_progress', 'approved', 'rejected', 'reverted'])->default('pending');
            $table->string('acting_doaa_email')->nullable();
            $table->string('vested_doaa_email')->nullable();
            $table->string('approved_by_authority')->nullable();
            $table->timestamps();
        });
    }

    // Reverse the migrations.
    public function down(): void
    {
        Schema::dropIfExists('pts6_forms');
    }
};
