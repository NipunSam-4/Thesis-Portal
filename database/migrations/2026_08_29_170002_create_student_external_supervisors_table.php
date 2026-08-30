<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_external_supervisors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('faculty_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['student_id', 'faculty_user_id'], 'student_ext_sup_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_external_supervisors');
    }
};
