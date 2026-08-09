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
        Schema::create('pts1_forms', function (Blueprint $table) {
            $table->id();
            
            // 1-to-1 Unique Relationship with Thesis
            $table->foreignId('thesis_id')->unique()->constrained('theses')->cascadeOnDelete();
            
            // Core Seminar & Form Details
            $table->date('seminar_date');
            $table->string('seminar_time');
            $table->string('seminar_venue');
            $table->string('meeting_link')->nullable();

            $table->boolean('publication_norm_fulfillment')->default(false);
            $table->boolean('special_approval_publication')->default(false);
            $table->string('publication_approval_doc_path')->nullable();

            $table->boolean('min_time_req_fulfilled')->default(false);
            $table->boolean('special_approval_min_time')->default(false);
            $table->string('min_time_approval_doc_path')->nullable();

            $table->string('draft_synopsis_report_doc_path');
            $table->string('publication_list_doc_path');
            $table->enum('work_status', ['adequate', 'inadequate'])->default('adequate');
            $table->text('main_supervisor_student_comment')->default('N/A');

            // Workflow Tracking
            $table->enum('current_stage', [
                'main_supervisor', 
                'co_supervisors', 
                'pspc_members', 
                'dpgc', 
                'hod', 
                'section_officer', 
                'doaa', 
                'completed', 
                'reverted',
                'rejected'
            ])->default('main_supervisor');

            $table->enum('status', ['in_progress', 'accepted', 'rejected', 'reverted'])->default('in_progress');
            $table->string('reverted_by_role')->nullable();
            $table->text('reversion_comment')->nullable();

            // 1. Main Supervisor Endorsement
            $table->boolean('main_supervisor_recommendation')->nullable();
            $table->text('main_supervisor_confidential_remark')->nullable();
            $table->timestamp('main_supervisor_submitted_at')->nullable();


            // 2. Co-Supervisors (Up to 3)
            $table->foreignId('co_supervisor_1_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_1_recommendation')->nullable();
            $table->text('co_supervisor_1_confidential_remark')->nullable();

            $table->foreignId('co_supervisor_2_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_2_recommendation')->nullable();
            $table->text('co_supervisor_2_confidential_remark')->nullable();

            $table->foreignId('co_supervisor_3_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_3_recommendation')->nullable();
            $table->text('co_supervisor_3_confidential_remark')->nullable();

            $table->foreignId('co_supervisor_4_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_4_recommendation')->nullable();
            $table->text('co_supervisor_4_confidential_remark')->nullable();

            $table->foreignId('co_supervisor_5_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_5_recommendation')->nullable();
            $table->text('co_supervisor_5_confidential_remark')->nullable();

            $table->foreignId('co_supervisor_6_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_6_recommendation')->nullable();
            $table->text('co_supervisor_6_confidential_remark')->nullable();

            $table->foreignId('co_supervisor_7_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_7_recommendation')->nullable();
            $table->text('co_supervisor_7_confidential_remark')->nullable();

            $table->foreignId('co_supervisor_8_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_8_recommendation')->nullable();
            $table->text('co_supervisor_8_confidential_remark')->nullable();

            $table->foreignId('co_supervisor_9_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_9_recommendation')->nullable();
            $table->text('co_supervisor_9_confidential_remark')->nullable();

            $table->foreignId('co_supervisor_10_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_10_recommendation')->nullable();
            $table->text('co_supervisor_10_confidential_remark')->nullable();

            $table->timestamp('co_supervisors_submitted_at')->nullable();

            // 3. PSPC Committee Members (Up to 3)
            $table->foreignId('pspc_member_1_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('pspc_member_1_recommendation')->nullable();
            $table->text('pspc_member_1_confidential_remark')->nullable();

            $table->foreignId('pspc_member_2_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('pspc_member_2_recommendation')->nullable();
            $table->text('pspc_member_2_confidential_remark')->nullable();

            $table->foreignId('pspc_member_3_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('pspc_member_3_recommendation')->nullable();
            $table->text('pspc_member_3_confidential_remark')->nullable();

            $table->foreignId('pspc_member_4_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('pspc_member_4_recommendation')->nullable();
            $table->text('pspc_member_4_confidential_remark')->nullable();

            $table->foreignId('pspc_member_5_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('pspc_member_5_recommendation')->nullable();
            $table->text('pspc_member_5_confidential_remark')->nullable();

            $table->foreignId('pspc_member_6_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('pspc_member_6_recommendation')->nullable();
            $table->text('pspc_member_6_confidential_remark')->nullable();

            $table->foreignId('pspc_member_7_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('pspc_member_7_recommendation')->nullable();
            $table->text('pspc_member_7_confidential_remark')->nullable();

            $table->foreignId('pspc_member_8_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('pspc_member_8_recommendation')->nullable();
            $table->text('pspc_member_8_confidential_remark')->nullable();

            $table->foreignId('pspc_member_9_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('pspc_member_9_recommendation')->nullable();
            $table->text('pspc_member_9_confidential_remark')->nullable();

            $table->foreignId('pspc_member_10_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('pspc_member_10_recommendation')->nullable();
            $table->text('pspc_member_10_confidential_remark')->nullable();

            $table->timestamp('pspc_members_submitted_at')->nullable();

            // 4. DPGC Convenor
            $table->text('dpgc_student_comment')->nullable();
            $table->boolean('dpgc_recommendation')->nullable();
            $table->text('dpgc_confidential_remark')->nullable();

            // 5. Head of Department (HOD)
            $table->text('hod_student_comment')->nullable();
            $table->boolean('hod_recommendation')->nullable();
            $table->text('hod_confidential_remark')->nullable();

            // 6. Section Officer
            $table->text('section_officer_student_comment')->nullable();
            $table->boolean('section_officer_recommendation')->nullable();
            $table->text('section_officer_confidential_remark')->nullable();

            // 7. Dean of Academic Affairs (DOAA)
            $table->text('doaa_student_comment')->nullable();
            $table->boolean('doaa_approval')->nullable();
            $table->text('doaa_confidential_remark')->nullable();
            $table->timestamp('pts1_submitted_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pts1_forms');
    }
};
