<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pts3_oeb_chairperson_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pts3_draft_id')->constrained('pts3_drafts')->cascadeOnDelete();
            $table->unsignedTinyInteger('slot')->nullable();

            $table->string('name')->nullable();
            $table->string('designation')->nullable();
            $table->string('department')->nullable();
            $table->string('email')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pts3_oeb_chairperson_drafts');
    }
};
