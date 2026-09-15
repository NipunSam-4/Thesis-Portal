<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pts3_oeb_chairpersons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pts3_form_id')->constrained('pts3_forms')->cascadeOnDelete();
            
            $table->string('name');
            $table->string('designation');
            $table->string('department');
            $table->string('email');

            $table->timestamps();

            $table->unique(['pts3_form_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pts3_oeb_chairpersons');
    }
};
