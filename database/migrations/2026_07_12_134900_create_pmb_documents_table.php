<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pmb_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pmb_applicant_id')->constrained('pmb_applicants')->onDelete('cascade');
            $table->string('type');
            $table->string('file_path');
            $table->string('original_name');
            $table->timestamp('uploaded_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pmb_documents');
    }
};
