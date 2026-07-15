<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('position_title');
            $table->string('location')->nullable();
            $table->enum('employment_type', ['full_time', 'part_time', 'internship', 'contract']);
            $table->string('salary_range')->nullable();
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->date('deadline')->nullable();
            $table->string('contact_email')->nullable();
            $table->enum('status', ['open', 'closed', 'draft'])->default('draft');
            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_postings');
    }
};
