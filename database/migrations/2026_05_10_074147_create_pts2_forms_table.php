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
        Schema::create('pts2_forms', function (Blueprint $table) {
            $table->id();
            
            // 1-to-1 Unique Relationship with Thesis
            $table->foreignId('thesis_id')->unique()->constrained('theses')->cascadeOnDelete();
            $table->text('thesis_title')->nullable();
            
            // Student Synopsis Upload
            $table->string('synopsis_report_doc_path');

            // Workflow Tracking
            $table->enum('current_stage', [
                'main_supervisor', 
                'co_supervisors',
                'section_officer', 
                'doaa', 
                'completed', 
                'reverted',
                'rejected'
            ])->default('main_supervisor');

            $table->enum('status', ['pending','in_progress', 'accepted', 'rejected', 'reverted'])->default('pending');
            $table->string('reverted_by_role')->nullable();
            $table->unsignedBigInteger('reverted_by_id')->nullable();
            $table->text('reversion_comment')->nullable();

            // Student Submission Details
            $table->float('course_credits_student');
            $table->date('date_of_submission')->nullable();
            $table->text('current_address');
            $table->string('alternate_email')->nullable();
            $table->string('recent_phone_number');
            $table->string('alternate_phone_number')->nullable();

            // Further Certified That (Student Declarations)
            $table->boolean('cert_prima_facie_case')->default(false);
            $table->boolean('cert_no_prior_degree_submission')->default(false);
            $table->boolean('collaborative_work_status')->default(false);
            $table->text('collaborative_work_details')->nullable();

            // 1. Main Supervisor Endorsement
            $table->boolean('main_supervisor_cert_prima_facie_case')->nullable();
            $table->boolean('main_supervisor_cert_no_prior_degree_submission')->nullable();
            $table->boolean('main_supervisor_collaborative_work_status')->nullable();
            $table->text('main_supervisor_collaborative_work_details')->nullable();
            $table->boolean('main_supervisor_recommendation')->nullable();
            $table->text('main_supervisor_student_comment')->nullable();
            $table->text('main_supervisor_confidential_remark')->nullable();
            $table->timestamp('main_supervisor_submitted_at')->nullable();

            // 2. Co-Supervisors (Up to 10)
            $table->foreignId('co_supervisor_1_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_1_recommendation')->nullable();
            $table->text('co_supervisor_1_student_comment')->nullable();
            $table->text('co_supervisor_1_confidential_remark')->nullable();
            $table->timestamp('co_supervisor_1_submitted_at')->nullable();

            $table->foreignId('co_supervisor_2_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_2_recommendation')->nullable();
            $table->text('co_supervisor_2_student_comment')->nullable();
            $table->text('co_supervisor_2_confidential_remark')->nullable();
            $table->timestamp('co_supervisor_2_submitted_at')->nullable();

            $table->foreignId('co_supervisor_3_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_3_recommendation')->nullable();
            $table->text('co_supervisor_3_student_comment')->nullable();
            $table->text('co_supervisor_3_confidential_remark')->nullable();
            $table->timestamp('co_supervisor_3_submitted_at')->nullable();

            $table->foreignId('co_supervisor_4_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_4_recommendation')->nullable();
            $table->text('co_supervisor_4_student_comment')->nullable();
            $table->text('co_supervisor_4_confidential_remark')->nullable();
            $table->timestamp('co_supervisor_4_submitted_at')->nullable();

            $table->foreignId('co_supervisor_5_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_5_recommendation')->nullable();
            $table->text('co_supervisor_5_student_comment')->nullable();
            $table->text('co_supervisor_5_confidential_remark')->nullable();
            $table->timestamp('co_supervisor_5_submitted_at')->nullable();

            $table->foreignId('co_supervisor_6_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_6_recommendation')->nullable();
            $table->text('co_supervisor_6_student_comment')->nullable();
            $table->text('co_supervisor_6_confidential_remark')->nullable();
            $table->timestamp('co_supervisor_6_submitted_at')->nullable();

            $table->foreignId('co_supervisor_7_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_7_recommendation')->nullable();
            $table->text('co_supervisor_7_student_comment')->nullable();
            $table->text('co_supervisor_7_confidential_remark')->nullable();
            $table->timestamp('co_supervisor_7_submitted_at')->nullable();

            $table->foreignId('co_supervisor_8_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_8_recommendation')->nullable();
            $table->text('co_supervisor_8_student_comment')->nullable();
            $table->text('co_supervisor_8_confidential_remark')->nullable();
            $table->timestamp('co_supervisor_8_submitted_at')->nullable();

            $table->foreignId('co_supervisor_9_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_9_recommendation')->nullable();
            $table->text('co_supervisor_9_student_comment')->nullable();
            $table->text('co_supervisor_9_confidential_remark')->nullable();
            $table->timestamp('co_supervisor_9_submitted_at')->nullable();

            $table->foreignId('co_supervisor_10_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_10_recommendation')->nullable();
            $table->text('co_supervisor_10_student_comment')->nullable();
            $table->text('co_supervisor_10_confidential_remark')->nullable();
            $table->timestamp('co_supervisor_10_submitted_at')->nullable();

            $table->timestamp('co_supervisors_submitted_at')->nullable();

            // 3. Section Officer (Academic Office) Verification
            $table->boolean('section_officer_is_verified')->nullable();
            $table->text('section_officer_verification_remark')->nullable();
            $table->float('section_officer_course_credits')->nullable();
            $table->timestamp('section_officer_submitted_at')->nullable();

            // 4. Dean of Academic Affairs (DOAA)
            $table->text('doaa_student_comment')->nullable();
            $table->boolean('doaa_approval')->nullable();
            $table->text('doaa_confidential_remark')->nullable();
            $table->timestamp('doaa_submitted_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pts2_forms');
    }
};
