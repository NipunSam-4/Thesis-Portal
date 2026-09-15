<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pts3_examiners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pts3_form_id')->constrained('pts3_forms')->cascadeOnDelete();
            $table->enum('type', ['indian', 'international']);
            
            $table->string('name');
            $table->string('designation');
            $table->string('organization');
            $table->text('postal_address');
            $table->string('email');
            
            // Phone storage format as in PTS-2 (number, country code, iso2)
            $table->string('phone_number')->nullable();
            $table->string('phone_country_code')->nullable()->default('+91');
            $table->string('phone_iso2')->nullable()->default('in');

            $table->string('website')->nullable();
            $table->text('research_area')->nullable();
            
            $table->timestamps();

            $table->unique(['pts3_form_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pts3_examiners');
    }
};
