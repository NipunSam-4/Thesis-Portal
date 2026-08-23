<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Run the migrations.
    public function up(): void
    {
        Schema::create('pts2_extensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thesis_id')->constrained('theses')->onDelete('cascade');
            $table->text('reason_for_extension');
            $table->date('extended_until_date');

            $table->enum('status', ['pending','in_progress', 'approved', 'reverted', 'rejected'])->default('pending');
            $table->enum('current_stage', ['main_supervisor', 'dpgc', 'hod', 'section_officer', 'doaa', 'reverted', 'completed'])->default('main_supervisor');

            $table->string('reverted_by_role')->nullable();
            $table->unsignedBigInteger('reverted_by_id')->nullable();
            $table->text('reversion_comment')->nullable();

            // Main Supervisor Evaluation
            $table->boolean('main_supervisor_recommendation')->nullable();
            $table->text('main_supervisor_confidential_remark')->nullable();
            $table->timestamp('main_supervisor_submitted_at')->nullable();

            // DPGC Evaluation
            $table->boolean('dpgc_recommendation')->nullable();
            $table->text('dpgc_confidential_remark')->nullable();
            $table->timestamp('dpgc_submitted_at')->nullable();

            // HOD Evaluation
            $table->boolean('hod_recommendation')->nullable();
            $table->text('hod_confidential_remark')->nullable();
            $table->timestamp('hod_submitted_at')->nullable();

            // Section Officer Evaluation
            $table->boolean('section_officer_recommendation')->nullable();
            $table->text('section_officer_confidential_remark')->nullable();
            $table->timestamp('section_officer_submitted_at')->nullable();

            // DOAA Evaluation (Final Decision)
            $table->boolean('doaa_recommendation')->nullable();
            $table->text('doaa_confidential_remark')->nullable();
            $table->text('doaa_student_comment')->nullable();
            $table->timestamp('doaa_submitted_at')->nullable();
            $table->date('approved_extended_until_date')->nullable();

            // Acting & Vested DOAA Assignment
            $table->string('acting_doaa_email')->nullable();
            $table->string('vested_doaa_email')->nullable();
            $table->string('approved_by_authority')->nullable();

            $table->timestamps();
        });
    }

    // Reverse the migrations.
    public function down(): void
    {
        Schema::dropIfExists('pts2_extensions');
    }
};
