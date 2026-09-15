<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Run the migrations.
    public function up(): void
    {
        Schema::create('pts4_forms', function (Blueprint $table) {
            $table->id();

            // Relationship with Thesis
            $table->foreignId('thesis_id')->constrained('theses')->cascadeOnDelete();
            $table->text('thesis_title')->nullable();

            // Student Thesis Upload (Up to 100MB)
            $table->string('thesis_doc_path');

            // Workflow Tracking
            $table->enum('current_stage', [
                'main_supervisor',
                'co_supervisors',
                'academic_office',
                'dr',
                'completed',
                'reverted'
            ])->default('main_supervisor');

            $table->enum('status', ['pending', 'in_progress', 'accepted', 'not_accepted', 'reverted'])->default('pending');
            $table->string('reverted_by_role')->nullable();
            $table->unsignedBigInteger('reverted_by_id')->nullable();
            $table->text('reversion_comment')->nullable();

            // Student Submission Contact Details (synced with student profile)
            $table->string('hindi_name')->nullable();
            $table->text('current_address')->nullable();
            $table->string('alternate_email')->nullable();
            $table->string('alternate_phone_number')->nullable();
            $table->string('alternate_phone_country_code')->nullable()->default('+91');
            $table->string('alternate_phone_iso2')->nullable()->default('in');

            // 1. Main Supervisor Evaluation & Document Replacement
            $table->text('main_supervisor_thesis_title')->nullable();
            $table->string('main_supervisor_thesis_doc_path')->nullable();
            $table->boolean('main_supervisor_recommendation')->nullable();
            $table->text('main_supervisor_student_comment')->nullable();
            $table->text('main_supervisor_confidential_remark')->nullable();
            $table->timestamp('main_supervisor_submitted_at')->nullable();

            // 2. Co-Supervisors (Up to 10 slots)
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

            // 3. Academic Office Verification
            $table->boolean('academic_office_is_verified')->nullable();
            $table->text('academic_office_verification_remark')->nullable();
            $table->text('academic_office_confidential_remark')->nullable();
            $table->timestamp('academic_office_submitted_at')->nullable();

            // 4. AR / DR Final Acceptance
            $table->boolean('dr_acceptance')->nullable();
            $table->text('dr_student_comment')->nullable();
            $table->text('dr_confidential_remark')->nullable();
            $table->timestamp('dr_submitted_at')->nullable();

            // Authority Snapshots
            $table->foreignId('main_supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('academic_office_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('dr_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('accepted_by_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    // Reverse the migrations.
    public function down(): void
    {
        Schema::dropIfExists('pts4_forms');
    }
};
