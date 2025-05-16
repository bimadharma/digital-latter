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
        // Di dalam migration
        Schema::table('surat', function (Blueprint $table) {
            $table->string('file_docx')->nullable();
            $table->string('file_pdf')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat', function (Blueprint $table) {
            $table->dropColumn('file_docx');
            $table->dropColumn('file_pdf');
        });
    }
};
