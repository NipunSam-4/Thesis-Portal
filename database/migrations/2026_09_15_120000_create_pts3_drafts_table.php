<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pts3_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thesis_id')->constrained('theses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('thesis_title')->nullable();

            // Indian Examiners (Slots 1 to 4) - all nullable for draft
            for ($i = 1; $i <= 4; $i++) {
                $table->string("indian_examiner_{$i}_email")->nullable();
                $table->boolean("indian_examiner_{$i}_has_consent")->nullable();
                $table->string("indian_examiner_{$i}_consent_doc_path")->nullable();
            }

            // International Examiners (Slots 1 to 4) - all nullable for draft
            for ($i = 1; $i <= 4; $i++) {
                $table->string("international_examiner_{$i}_email")->nullable();
                $table->boolean("international_examiner_{$i}_has_consent")->nullable();
                $table->string("international_examiner_{$i}_consent_doc_path")->nullable();
            }

            // Proposed Oral Examination Board (OEB) Chairpersons (Slots 1 to 4) - all nullable for draft
            for ($i = 1; $i <= 4; $i++) {
                $table->string("oeb_chairperson_{$i}_email")->nullable();
            }

            $table->timestamps();

            $table->unique(['thesis_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pts3_drafts');
    }
};
