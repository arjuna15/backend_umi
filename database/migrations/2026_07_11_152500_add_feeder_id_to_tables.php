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
            $table->string('feeder_id')->nullable()->after('status');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->string('feeder_id')->nullable()->after('prodi');
        });

        Schema::table('krs_submissions', function (Blueprint $table) {
            $table->string('feeder_id')->nullable()->after('status');
        });

        Schema::table('grades', function (Blueprint $table) {
            $table->string('feeder_id')->nullable()->after('nilai_huruf');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('feeder_id');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('feeder_id');
        });

        Schema::table('krs_submissions', function (Blueprint $table) {
            $table->dropColumn('feeder_id');
        });

        Schema::table('grades', function (Blueprint $table) {
            $table->dropColumn('feeder_id');
        });
    }
};
