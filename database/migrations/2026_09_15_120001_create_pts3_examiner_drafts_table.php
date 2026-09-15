<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pts3_examiner_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pts3_draft_id')->constrained('pts3_drafts')->cascadeOnDelete();
            $table->unsignedTinyInteger('slot')->nullable();
            $table->enum('type', ['indian', 'international'])->nullable();

            $table->string('name')->nullable();
            $table->string('designation')->nullable();
            $table->string('organization')->nullable();
            $table->text('postal_address')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('phone_country_code', 10)->default('+91')->nullable();
            $table->string('phone_iso2', 10)->default('in')->nullable();
            $table->string('website')->nullable();
            $table->string('research_area')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pts3_examiner_drafts');
    }
};
