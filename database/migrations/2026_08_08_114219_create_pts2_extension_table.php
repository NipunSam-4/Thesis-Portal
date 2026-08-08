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
        Schema::create('pts2_extension', function (Blueprint $table) {
            $table->foreignId('thesis_id')->unique()->constrained('theses')->cascadeOnDelete();
            
            // Student Reason for extension
            $table->text('student_extension_reason')->default('N/A');

            // Workflow Tracking
            $table->enum('current_stage', [
                'main_supervisor', 
                'dpgc', 
                'hod', 
                'section_officer', 
                'adoaa', 
                'completed', 
                'rejected'
            ])->default('main_supervisor');

            $table->enum('status', ['in_progress', 'accepted', 'rejected'])->default('in_progress');
            $table->string('rejected_by_role')->nullable();

            // 1. Main Supervisor Endorsement
            $table->text('main_supervisor_student_comment')->default('N/A');
            $table->boolean('main_supervisor_recommendation')->default(false);
            $table->text('main_supervisor_confidential_remark')->nullable();
            $table->timestamp('main_supervisor_submitted_at')->nullable();

            // 4. DPGC Convenor
            $table->text('dpgc_student_comment')->nullable();
            $table->boolean('dpgc_recommendation')->default(false);
            $table->text('dpgc_confidential_remark')->nullable();

            // 5. Head of Department (HOD)
            $table->text('hod_student_comment')->nullable();
            $table->boolean('hod_recommendation')->default(false);
            $table->text('hod_confidential_remark')->nullable();

            // 6. Academic Office
            $table->text('academic_office_student_comment')->nullable();
            $table->boolean('academic_office_recommendation')->default(false);
            $table->text('academic_office_confidential_remark')->nullable();

            // 7. Associate Dean of Academic Affairs (DOAA)
            $table->text('adoaa_student_comment')->nullable();
            $table->boolean('adoaa_approval')->default(false);
            $table->text('adoaa_confidential_remark')->nullable();
            $table->timestamp('pts2_extension_submitted_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pts2_extension');
    }
};
