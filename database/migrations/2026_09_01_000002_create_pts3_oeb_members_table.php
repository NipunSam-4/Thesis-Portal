<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pts3_oeb_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pts3_form_id')->constrained('pts3_forms')->cascadeOnDelete();
            
            $table->string('name');
            $table->string('designation');
            $table->string('department');
            $table->string('email');
            
            // Phone storage format as in PTS-2 (number, country code, iso2)
            $table->string('phone_number')->nullable();
            $table->string('phone_country_code')->nullable()->default('+91');
            $table->string('phone_iso2')->nullable()->default('in');

            $table->text('academic_office_remark')->nullable();
            $table->text('doaa_remark')->nullable();

            // OEB Member Selection Priorities for DOAA & Senate Chairperson stages
            $table->unsignedInteger('doaa_priority')->nullable();
            $table->unsignedInteger('senate_chairperson_priority')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pts3_oeb_members');
    }
};
