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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('mahasiswa'); // admin, dosen, mahasiswa
            $table->string('nim_nip')->nullable()->unique();
            $table->string('prodi')->nullable();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->integer('sks');
            $table->foreignId('dosen_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('prodi')->nullable();
            $table->string('semester')->nullable(); // e.g. "Ganjil 2026/2027"
            $table->timestamps();
        });

        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->decimal('score', 5, 2)->nullable();
            $table->string('grade', 2)->nullable(); // A, B+, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
        Schema::dropIfExists('courses');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'nim_nip', 'prodi']);
        });
    }
};
