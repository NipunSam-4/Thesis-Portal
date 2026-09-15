<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pts3_forms', function (Blueprint $table) {
            $table->id();
            
            // Relationship with Thesis
            $table->foreignId('thesis_id')->constrained('theses')->cascadeOnDelete();
            
            // Thesis Title
            $table->text('thesis_title')->nullable();

            // Workflow Tracking
            $table->enum('current_stage', [
                'main_supervisor', 
                'co_supervisors',
                'dpgc',
                'hod',
                'academic_office', 
                'doaa',
                'senate_chairperson',
                'completed', 
                'reverted'
            ])->default('main_supervisor');

            $table->enum('status', ['pending', 'in_progress', 'approved', 'rejected', 'reverted'])->default('pending');
            $table->string('reverted_by_role')->nullable();
            $table->unsignedBigInteger('reverted_by_id')->nullable();
            $table->text('reversion_comment')->nullable();

            // 1. Main Supervisor
            $table->boolean('main_supervisor_recommendation')->nullable();
            $table->timestamp('main_supervisor_submitted_at')->nullable();

            // 2. Co-Supervisors (Up to 10 - includes External Supervisors as in PTS-2)
            for ($i = 1; $i <= 10; $i++) {
                $table->foreignId("co_supervisor_{$i}_id")->nullable()->constrained('users')->nullOnDelete();
                $table->boolean("co_supervisor_{$i}_recommendation")->nullable();
                $table->timestamp("co_supervisor_{$i}_submitted_at")->nullable();
            }
            $table->timestamp('co_supervisors_submitted_at')->nullable();
            
            // 3. DPGC (Declaration-based Recommendation)
            $table->boolean('dpgc_recommendation')->nullable();
            $table->timestamp('dpgc_submitted_at')->nullable();

            // 4. HOD (Declaration-based Recommendation)
            $table->boolean('hod_recommendation')->nullable();
            $table->timestamp('hod_submitted_at')->nullable();

            // 6. Academic Office
            $table->boolean('academic_office_is_verified')->nullable();
            $table->text('academic_office_verification_remark')->nullable();
            $table->timestamp('academic_office_submitted_at')->nullable();

            // 7. DOAA
            $table->boolean('doaa_is_verified')->nullable();
            $table->text('doaa_verification_remark')->nullable();
            $table->timestamp('doaa_submitted_at')->nullable();

            // 7. Senate Chairperson (Declaration-based Approval)
            $table->boolean('senate_chairperson_approval')->nullable();
            $table->text('senate_chairperson_confidential_remark')->nullable(); // For DOAA only
            $table->text('senate_chairperson_approval_remark')->nullable(); // For all
            $table->timestamp('senate_chairperson_submitted_at')->nullable();

            // Authority Snapshots & Vested DOAA Assignment
            $table->foreignId('main_supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('dpgc_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('hod_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('academic_office_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('doaa_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('senate_chairperson_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('acting_doaa_email')->nullable();
            $table->string('vested_doaa_email')->nullable();
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->nullOnDelete();

            // 8. Panel of Examiners (Up to 4 Indian, 4 International) & OEB Chairpersons (Up to 4)
            for ($i = 1; $i <= 4; $i++) {
                $table->string("indian_examiner_{$i}_email")->nullable();
                $table->boolean("indian_examiner_{$i}_has_consent")->nullable();
                $table->string("indian_examiner_{$i}_consent_doc_path")->nullable();
                $table->text("indian_examiner_{$i}_academic_office_remark")->nullable();
                $table->text("indian_examiner_{$i}_doaa_remark")->nullable();
                $table->unsignedInteger("indian_examiner_{$i}_doaa_priority")->nullable();
                $table->unsignedInteger("indian_examiner_{$i}_senate_chairperson_priority")->nullable();
            }

            for ($i = 1; $i <= 4; $i++) {
                $table->string("international_examiner_{$i}_email")->nullable();
                $table->boolean("international_examiner_{$i}_has_consent")->nullable();
                $table->string("international_examiner_{$i}_consent_doc_path")->nullable();
                $table->text("international_examiner_{$i}_academic_office_remark")->nullable();
                $table->text("international_examiner_{$i}_doaa_remark")->nullable();
                $table->unsignedInteger("international_examiner_{$i}_doaa_priority")->nullable();
                $table->unsignedInteger("international_examiner_{$i}_senate_chairperson_priority")->nullable();
            }

            for ($i = 1; $i <= 4; $i++) {
                $table->string("oeb_chairperson_{$i}_email")->nullable();
                $table->text("oeb_chairperson_{$i}_academic_office_remark")->nullable();
                $table->text("oeb_chairperson_{$i}_doaa_remark")->nullable();
                $table->unsignedInteger("oeb_chairperson_{$i}_doaa_priority")->nullable();
                $table->unsignedInteger("oeb_chairperson_{$i}_senate_chairperson_priority")->nullable();
            }

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pts3_forms');
    }
};
