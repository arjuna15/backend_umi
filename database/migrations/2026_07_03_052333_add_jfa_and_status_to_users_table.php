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
            $table->string('jfa')->default('Asisten Ahli'); // Asisten Ahli, Lektor, Lektor Kepala, Guru Besar
            $table->string('status')->default('Aktif'); // Aktif, Cuti, Studi Lanjut, Nonaktif
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['jfa', 'status']);
        });
    }
};
