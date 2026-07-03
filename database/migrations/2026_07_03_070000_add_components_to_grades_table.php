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
        Schema::table('grades', function (Blueprint $table) {
            $table->decimal('attendance_score', 5, 2)->nullable()->after('course_id');
            $table->decimal('assignment_score', 5, 2)->nullable()->after('attendance_score');
            $table->decimal('uts_score', 5, 2)->nullable()->after('assignment_score');
            $table->decimal('uas_score', 5, 2)->nullable()->after('uts_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropColumn([
                'attendance_score',
                'assignment_score',
                'uts_score',
                'uas_score'
            ]);
        });
    }
};
