<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Run the migrations.
    public function up(): void
    {
        Schema::create('comment_snippets', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->enum('form_type', [ 
                'pts1', 
                'pts2', 
                'pts2_extension',
                'pts3',
                'pts4',
                'pts5',
                'pts6',
                'all'
            ])->default('all');

            $table->enum('comment_type', [ 
                'recommendation',
                'non-recommendation',
                'student_comment',
                'verification_remark',
                'reversion_comment'
            ])->default('recommendation');

            $table->enum('role', [ 
                'dpgc', 
                'hod', 
                'all_dept',
                'academic_office',
                'section_officer', 
                'ar',
                'dr',
                'doaa', 
                'senate_chairperson',
                'all_global'
            ])->default('all_global');
            
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    // Reverse the migrations.
    public function down(): void
    {
        Schema::dropIfExists('comment_snippets');
    }
};
