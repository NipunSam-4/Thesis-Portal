<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Run the migrations.
    public function up(): void
    {
            Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thesis_id')->constrained()->cascadeOnDelete();
            $table->enum('form_type', ['PTS-1', 'PTS-2', 'PTS-3', 'PTS-4', 'PTS-5']);
            $table->string('document_path')->nullable();
            $table->enum('status', [ 'Reverted', 'approved', 'Rejected']); // Rejected only for PTS-1
            $table->timestamps();
        });
    }

    // Reverse the migrations.
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
