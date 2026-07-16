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
        Schema::table('classrooms', function (Blueprint $table) {
            $table->string('campus_location')->default('bintaro'); // bintaro, pasar_minggu
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->string('campus_location')->default('bintaro'); // bintaro, pasar_minggu
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropColumn('campus_location');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('campus_location');
        });
    }
};
