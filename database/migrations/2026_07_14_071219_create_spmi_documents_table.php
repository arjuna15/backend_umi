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
        Schema::create('spmi_documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('category', ['standar', 'audit', 'evaluasi', 'akreditasi']);
            $table->string('file_path');
            $table->string('academic_year');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spmi_documents');
    }
};
