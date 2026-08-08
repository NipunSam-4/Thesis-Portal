<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_supervisor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('faculty_user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('supervisor_type', ['main', 'co']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_supervisor');
    }
};