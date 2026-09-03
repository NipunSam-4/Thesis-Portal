<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Run the migrations.
    public function up(): void
    {
        Schema::create('pts1_forms', function (Blueprint $table) {
            $table->id();

            $table->foreignId('thesis_id')->constrained('theses')->cascadeOnDelete();
            $table->text('thesis_title')->nullable();
            
            // Core Seminar & Form Details
            $table->date('seminar_date');
            $table->string('seminar_time');
            $table->string('seminar_venue');
            $table->string('meeting_link')->nullable();

            $table->boolean('publication_norm_fulfillment')->default(false);
            $table->boolean('special_approval_publication')->nullable();
            $table->string('publication_approval_doc_path')->nullable();

            $table->boolean('min_time_req_fulfilled')->default(false);
            $table->boolean('special_approval_min_time')->nullable();
            $table->string('min_time_approval_doc_path')->nullable();

            $table->string('draft_synopsis_report_doc_path');
            $table->string('publication_list_doc_path');
            $table->enum('work_status', ['adequate', 'inadequate'])->default('adequate');
            $table->text('main_supervisor_student_comment')->nullable();

            // Workflow Tracking
            $table->enum('current_stage', [
                'main_supervisor', 
                'co_supervisors', 
                'pspc_members', 
                'dpgc', 
                'hod', 
                'academic_office', 
                'doaa', 
                'completed', 
                'reverted'
            ])->default('main_supervisor');

            $table->enum('status', ['pending', 'in_progress', 'approved', 'rejected', 'reverted'])->default('pending');
            $table->string('reverted_by_role')->nullable();
            $table->unsignedBigInteger('reverted_by_id')->nullable();
            $table->text('reversion_comment')->nullable();

            // 1. Main Supervisor Endorsement & Edited Fields
            $table->text('main_supervisor_thesis_title')->nullable();
            $table->date('main_supervisor_seminar_date')->nullable();
            $table->string('main_supervisor_seminar_time')->nullable();
            $table->string('main_supervisor_seminar_venue')->nullable();
            $table->string('main_supervisor_meeting_link')->nullable();
            $table->boolean('main_supervisor_publication_norm_fulfillment')->nullable();
            $table->boolean('main_supervisor_special_approval_publication')->nullable();
            $table->boolean('main_supervisor_min_time_req_fulfilled')->nullable();
            $table->boolean('main_supervisor_special_approval_min_time')->nullable();
            $table->date('main_supervisor_date_confirmation')->nullable();

            $table->boolean('main_supervisor_recommendation')->nullable();
            $table->text('main_supervisor_confidential_remark')->nullable();
            $table->timestamp('main_supervisor_submitted_at')->nullable();
            $table->string('main_supervisor_draft_synopsis_report_doc_path')->nullable();
            $table->string('main_supervisor_publication_list_doc_path')->nullable();
            $table->string('main_supervisor_publication_approval_doc_path')->nullable();
            $table->string('main_supervisor_min_time_approval_doc_path')->nullable();


            // 2. Co-Supervisors (Up to 10)
            $table->foreignId('co_supervisor_1_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_1_recommendation')->nullable();
            $table->text('co_supervisor_1_confidential_remark')->nullable();
            $table->timestamp('co_supervisor_1_submitted_at')->nullable();

            $table->foreignId('co_supervisor_2_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_2_recommendation')->nullable();
            $table->text('co_supervisor_2_confidential_remark')->nullable();
            $table->timestamp('co_supervisor_2_submitted_at')->nullable();

            $table->foreignId('co_supervisor_3_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_3_recommendation')->nullable();
            $table->text('co_supervisor_3_confidential_remark')->nullable();
            $table->timestamp('co_supervisor_3_submitted_at')->nullable();

            $table->foreignId('co_supervisor_4_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_4_recommendation')->nullable();
            $table->text('co_supervisor_4_confidential_remark')->nullable();
            $table->timestamp('co_supervisor_4_submitted_at')->nullable();

            $table->foreignId('co_supervisor_5_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_5_recommendation')->nullable();
            $table->text('co_supervisor_5_confidential_remark')->nullable();
            $table->timestamp('co_supervisor_5_submitted_at')->nullable();

            $table->foreignId('co_supervisor_6_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_6_recommendation')->nullable();
            $table->text('co_supervisor_6_confidential_remark')->nullable();
            $table->timestamp('co_supervisor_6_submitted_at')->nullable();

            $table->foreignId('co_supervisor_7_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_7_recommendation')->nullable();
            $table->text('co_supervisor_7_confidential_remark')->nullable();
            $table->timestamp('co_supervisor_7_submitted_at')->nullable();

            $table->foreignId('co_supervisor_8_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_8_recommendation')->nullable();
            $table->text('co_supervisor_8_confidential_remark')->nullable();
            $table->timestamp('co_supervisor_8_submitted_at')->nullable();

            $table->foreignId('co_supervisor_9_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_9_recommendation')->nullable();
            $table->text('co_supervisor_9_confidential_remark')->nullable();
            $table->timestamp('co_supervisor_9_submitted_at')->nullable();

            $table->foreignId('co_supervisor_10_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_10_recommendation')->nullable();
            $table->text('co_supervisor_10_confidential_remark')->nullable();
            $table->timestamp('co_supervisor_10_submitted_at')->nullable();

            $table->timestamp('co_supervisors_submitted_at')->nullable();

            // 3. PSPC Committee Members (Up to 10)
            $table->foreignId('pspc_member_1_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('pspc_member_1_recommendation')->nullable();
            $table->text('pspc_member_1_confidential_remark')->nullable();
            $table->timestamp('pspc_member_1_submitted_at')->nullable();

            $table->foreignId('pspc_member_2_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('pspc_member_2_recommendation')->nullable();
            $table->text('pspc_member_2_confidential_remark')->nullable();
            $table->timestamp('pspc_member_2_submitted_at')->nullable();

            $table->foreignId('pspc_member_3_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('pspc_member_3_recommendation')->nullable();
            $table->text('pspc_member_3_confidential_remark')->nullable();
            $table->timestamp('pspc_member_3_submitted_at')->nullable();

            $table->foreignId('pspc_member_4_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('pspc_member_4_recommendation')->nullable();
            $table->text('pspc_member_4_confidential_remark')->nullable();
            $table->timestamp('pspc_member_4_submitted_at')->nullable();

            $table->foreignId('pspc_member_5_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('pspc_member_5_recommendation')->nullable();
            $table->text('pspc_member_5_confidential_remark')->nullable();
            $table->timestamp('pspc_member_5_submitted_at')->nullable();

            $table->foreignId('pspc_member_6_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('pspc_member_6_recommendation')->nullable();
            $table->text('pspc_member_6_confidential_remark')->nullable();
            $table->timestamp('pspc_member_6_submitted_at')->nullable();

            $table->foreignId('pspc_member_7_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('pspc_member_7_recommendation')->nullable();
            $table->text('pspc_member_7_confidential_remark')->nullable();
            $table->timestamp('pspc_member_7_submitted_at')->nullable();

            $table->foreignId('pspc_member_8_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('pspc_member_8_recommendation')->nullable();
            $table->text('pspc_member_8_confidential_remark')->nullable();
            $table->timestamp('pspc_member_8_submitted_at')->nullable();

            $table->foreignId('pspc_member_9_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('pspc_member_9_recommendation')->nullable();
            $table->text('pspc_member_9_confidential_remark')->nullable();
            $table->timestamp('pspc_member_9_submitted_at')->nullable();

            $table->foreignId('pspc_member_10_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('pspc_member_10_recommendation')->nullable();
            $table->text('pspc_member_10_confidential_remark')->nullable();
            $table->timestamp('pspc_member_10_submitted_at')->nullable();

            $table->timestamp('pspc_members_submitted_at')->nullable();

            // 4. DPGC Convenor
            $table->text('dpgc_student_comment')->nullable();
            $table->boolean('dpgc_recommendation')->nullable();
            $table->text('dpgc_confidential_remark')->nullable();
            $table->timestamp('dpgc_submitted_at')->nullable();

            // 5. Head of Department (HOD)
            $table->text('hod_student_comment')->nullable();
            $table->boolean('hod_recommendation')->nullable();
            $table->text('hod_confidential_remark')->nullable();
            $table->timestamp('hod_submitted_at')->nullable();

            // 6. Academic Office Verification
            $table->boolean('academic_office_is_verified')->nullable();
            $table->text('academic_office_confidential_remark')->nullable();
            $table->timestamp('academic_office_submitted_at')->nullable();

            // 7. Dean of Academic Affairs (DOAA)
            $table->text('doaa_student_comment')->nullable();
            $table->boolean('doaa_approval')->nullable();
            $table->text('doaa_confidential_remark')->nullable();
            $table->timestamp('doaa_submitted_at')->nullable();

            // Acting & Vested DOAA Assignment
            $table->foreignId('main_supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('dpgc_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('hod_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('academic_office_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('doaa_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('acting_doaa_email')->nullable();
            $table->string('vested_doaa_email')->nullable();
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    // Reverse the migrations.
    public function down(): void
    {
        Schema::dropIfExists('pts1_forms');
    }
};
