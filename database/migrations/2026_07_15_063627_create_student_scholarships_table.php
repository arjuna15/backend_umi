<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_scholarships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Hubung ke mahasiswa
            $table->foreignId('scholarship_id')->constrained('scholarships')->onDelete('cascade');
            $table->string('start_semester'); // Mulai aktif (contoh: Ganjil 2026/2027)
            $table->string('end_semester')->nullable(); // Semester beasiswa dicabut atau selesai
            $table->enum('status', ['active', 'revoked', 'completed'])->default('active');
            $table->text('notes')->nullable(); // Keterangan (Alasan dicabut, beasiswa pengganti, dll)
            $table->string('sk_number')->nullable(); // Nomor SK Penerima Beasiswa
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_scholarships');
    }
};
