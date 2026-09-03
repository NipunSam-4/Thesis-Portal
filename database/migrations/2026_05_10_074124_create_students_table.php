<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Run the migrations.
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->enum('program_name',['phd','msr'])->default('phd');
            $table->string('roll_number')->unique();
            $table->string('date_joining');
            $table->string('date_registration');
            $table->string('date_confirmation')->nullable();
            $table->string('admission_category')->nullable()->default('TA');
            $table->float('course_credits_required')->nullable()->default(0);
            $table->float('course_credits_earned')->nullable()->default(0);
            $table->string('phone_number')->nullable();
            $table->string('phone_country_code')->nullable()->default('+91');
            $table->string('phone_iso2')->nullable()->default('in');
            $table->string('alternate_phone_number')->nullable();
            $table->string('alternate_phone_country_code')->nullable()->default('+91');
            $table->string('alternate_phone_iso2')->nullable()->default('in');
            $table->string('alternate_email')->nullable();
            $table->string('hindi_name')->nullable();
            $table->text('current_address')->nullable();
            $table->timestamps();
        });
    }

    // Reverse the migrations.
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
