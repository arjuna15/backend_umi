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
        if (!Schema::hasColumn('grades', 'is_published')) {
            Schema::table('grades', function (Blueprint $table) {
                $table->boolean('is_published')->default(true)->after('grade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('grades', 'is_published')) {
            Schema::table('grades', function (Blueprint $table) {
                $table->dropColumn('is_published');
            });
        }
    }
};
