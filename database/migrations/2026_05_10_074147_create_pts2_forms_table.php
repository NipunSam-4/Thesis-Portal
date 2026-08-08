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
            
            // Student Synopsis Upload
            $table->string('synopsis_report_doc_path');
            $table->text('remarks')->default('N/A');

            // Workflow Tracking
            $table->enum('current_stage', [
                'main_supervisor', 
                'co_supervisors',
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

            // 1. Main Supervisor Endorsement
            $table->text('main_supervisor_student_comment')->default('N/A');
            $table->boolean('main_supervisor_recommendation')->default(false);
            $table->text('main_supervisor_confidential_remark')->nullable();
            $table->text('main_supervisor_reversion_comment')->nullable();
            $table->timestamp('main_supervisor_submitted_at')->nullable();

            // 2. Co-Supervisors (Up to 10)
            $table->foreignId('co_supervisor_1_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_1_recommendation')->default(false);
            $table->text('co_supervisor_1_confidential_remark')->nullable();
            $table->text('co_supervisor_1_reversion_comment')->nullable();

            $table->foreignId('co_supervisor_2_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_2_recommendation')->default(false);
            $table->text('co_supervisor_2_confidential_remark')->nullable();
            $table->text('co_supervisor_2_reversion_comment')->nullable();

            $table->foreignId('co_supervisor_3_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_3_recommendation')->default(false);
            $table->text('co_supervisor_3_confidential_remark')->nullable();
            $table->text('co_supervisor_3_reversion_comment')->nullable();

            $table->foreignId('co_supervisor_4_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_4_recommendation')->default(false);
            $table->text('co_supervisor_4_confidential_remark')->nullable();
            $table->text('co_supervisor_4_reversion_comment')->nullable();

            $table->foreignId('co_supervisor_5_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_5_recommendation')->default(false);
            $table->text('co_supervisor_5_confidential_remark')->nullable();
            $table->text('co_supervisor_5_reversion_comment')->nullable();

            $table->foreignId('co_supervisor_6_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_6_recommendation')->default(false);
            $table->text('co_supervisor_6_confidential_remark')->nullable();
            $table->text('co_supervisor_6_reversion_comment')->nullable();

            $table->foreignId('co_supervisor_7_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_7_recommendation')->default(false);
            $table->text('co_supervisor_7_confidential_remark')->nullable();
            $table->text('co_supervisor_7_reversion_comment')->nullable();

            $table->foreignId('co_supervisor_8_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_8_recommendation')->default(false);
            $table->text('co_supervisor_8_confidential_remark')->nullable();
            $table->text('co_supervisor_8_reversion_comment')->nullable();

            $table->foreignId('co_supervisor_9_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_9_recommendation')->default(false);
            $table->text('co_supervisor_9_confidential_remark')->nullable();
            $table->text('co_supervisor_9_reversion_comment')->nullable();

            $table->foreignId('co_supervisor_10_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_10_recommendation')->default(false);
            $table->text('co_supervisor_10_confidential_remark')->nullable();
            $table->text('co_supervisor_10_reversion_comment')->nullable();

            $table->timestamp('co_supervisors_submitted_at')->nullable();

            // 3. PSPC Committee Members (Up to 10)
            $table->foreignId('pspc_member_1_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pspc_member_2_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pspc_member_3_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pspc_member_4_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pspc_member_5_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pspc_member_6_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pspc_member_7_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pspc_member_8_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pspc_member_9_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pspc_member_10_id')->nullable()->constrained('users')->nullOnDelete();

            // 4. DPGC Convenor
            $table->text('dpgc_student_comment')->nullable();
            $table->boolean('dpgc_recommendation')->default(false);
            $table->text('dpgc_confidential_remark')->nullable();
            $table->text('dpgc_reversion_comment')->nullable();

            // 5. Head of Department (HOD)
            $table->text('hod_student_comment')->nullable();
            $table->boolean('hod_recommendation')->default(false);
            $table->text('hod_confidential_remark')->nullable();
            $table->text('hod_reversion_comment')->nullable();

            // 6. Academic Office
            $table->text('academic_office_student_comment')->nullable();
            $table->floatval('course_credits')->default('0.0');
            $table->boolean('academic_office_recommendation')->default(false);
            $table->text('academic_office_confidential_remark')->nullable();
            $table->text('academic_office_reversion_comment')->nullable();

            // 7. Dean of Academic Affairs (DOAA)
            $table->text('doaa_student_comment')->nullable();
            $table->boolean('doaa_approval')->default(false);
            $table->text('doaa_confidential_remark')->nullable();
            $table->text('doaa_reversion_comment')->nullable();
            $table->timestamp('pts2_submitted_at')->nullable();

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
