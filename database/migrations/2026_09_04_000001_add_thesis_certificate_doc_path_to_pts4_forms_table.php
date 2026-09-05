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
        Schema::table('pts4_forms', function (Blueprint $table) {
            $table->string('thesis_certificate_doc_path')->nullable()->after('thesis_doc_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pts4_forms', function (Blueprint $table) {
            $table->dropColumn('thesis_certificate_doc_path');
        });
    }
};
