<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppg_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('nip')->nullable();
            $table->string('school_origin');
            $table->string('subject');
            $table->string('batch');
            $table->enum('status', ['registered', 'active', 'completed', 'dropped'])->default('registered');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('certificate_number')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppg_participants');
    }
};
