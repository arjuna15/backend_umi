<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rpl_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('applicant_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('previous_institution');
            $table->string('previous_program');
            $table->string('target_program');
            $table->unsignedInteger('work_experience_years')->default(0);
            $table->enum('status', ['pending', 'review', 'approved', 'rejected'])->default('pending');
            $table->unsignedInteger('credits_recognized')->default(0);
            $table->text('reviewer_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('document_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rpl_applications');
    }
};
