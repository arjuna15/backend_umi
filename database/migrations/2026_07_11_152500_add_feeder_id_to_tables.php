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
        if (!Schema::hasColumn('users', 'feeder_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('feeder_id')->nullable()->after('status');
            });
        }

        if (!Schema::hasColumn('courses', 'feeder_id')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->string('feeder_id')->nullable()->after('prodi');
            });
        }

        if (!Schema::hasColumn('krs_submissions', 'feeder_id')) {
            Schema::table('krs_submissions', function (Blueprint $table) {
                $table->string('feeder_id')->nullable()->after('status');
            });
        }

        if (!Schema::hasColumn('grades', 'feeder_id')) {
            Schema::table('grades', function (Blueprint $table) {
                $table->string('feeder_id')->nullable()->after('grade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'feeder_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('feeder_id');
            });
        }

        if (Schema::hasColumn('courses', 'feeder_id')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn('feeder_id');
            });
        }

        if (Schema::hasColumn('krs_submissions', 'feeder_id')) {
            Schema::table('krs_submissions', function (Blueprint $table) {
                $table->dropColumn('feeder_id');
            });
        }

        if (Schema::hasColumn('grades', 'feeder_id')) {
            Schema::table('grades', function (Blueprint $table) {
                $table->dropColumn('feeder_id');
            });
        }
    }
};
