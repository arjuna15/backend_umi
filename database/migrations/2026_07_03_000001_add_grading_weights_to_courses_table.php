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
        Schema::table('courses', function (Blueprint $table) {
            $table->decimal('attendance_weight', 5, 2)->default(10)->after('semester');
            $table->decimal('assignment_weight', 5, 2)->default(20)->after('attendance_weight');
            $table->decimal('uts_weight', 5, 2)->default(30)->after('assignment_weight');
            $table->decimal('uas_weight', 5, 2)->default(40)->after('uts_weight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'attendance_weight',
                'assignment_weight',
                'uts_weight',
                'uas_weight',
            ]);
        });
    }
};
