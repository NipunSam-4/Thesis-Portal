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
            $table->text('additional_comments')->default('N/A');

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
                'rejected'
            ])->default('main_supervisor');

            $table->enum('status', ['in_progress', 'accepted', 'rejected', 'reverted'])->default('in_progress');
            $table->string('reverted_by_role')->nullable();

            // 1. Main Supervisor Endorsement
            $table->boolean('main_supervisor_endorsement')->default(false);
            $table->text('main_supervisor_comment')->default('N/A');

            // 2. Co-Supervisors (Up to 3)
            $table->foreignId('co_supervisor_1_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_1_endorsement')->default(false);
            $table->text('co_supervisor_1_comment')->default('N/A');

            $table->foreignId('co_supervisor_2_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_2_endorsement')->default(false);
            $table->text('co_supervisor_2_comment')->default('N/A');

            $table->foreignId('co_supervisor_3_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('co_supervisor_3_endorsement')->default(false);
            $table->text('co_supervisor_3_comment')->default('N/A');

            // 3. PSPC Committee Members (Up to 3)
            $table->foreignId('pspc_member_1_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('pspc_member_1_endorsement')->default(false);
            $table->text('pspc_member_1_comment')->default('N/A');

            $table->foreignId('pspc_member_2_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('pspc_member_2_endorsement')->default(false);
            $table->text('pspc_member_2_comment')->default('N/A');

            $table->foreignId('pspc_member_3_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('pspc_member_3_endorsement')->default(false);
            $table->text('pspc_member_3_comment')->default('N/A');

            // 4. DPGC Convenor
            $table->boolean('dpgc_endorsement')->default(false);
            $table->text('dpgc_comment')->default('N/A');

            // 5. Head of Department (HOD)
            $table->boolean('hod_endorsement')->default(false);
            $table->text('hod_comment')->default('N/A');

            // 6. Section Officer
            $table->boolean('section_officer_endorsement')->default(false);
            $table->text('section_officer_comment')->default('N/A');

            // 7. Dean of Academic Affairs (DOAA)
            $table->boolean('doaa_endorsement')->default(false);
            $table->text('doaa_comment')->default('N/A');

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
