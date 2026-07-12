<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pmb_applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pmb_period_id')->constrained('pmb_periods')->onDelete('cascade');
            $table->string('registration_number')->unique();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->enum('gender', ['L', 'P']);
            $table->date('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->text('address')->nullable();
            $table->string('school_origin')->nullable();
            $table->string('program_choice');
            $table->enum('status', ['pending', 'verified', 'accepted', 'rejected', 'enrolled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pmb_applicants');
    }
};
