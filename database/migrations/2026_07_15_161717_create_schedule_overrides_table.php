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
        Schema::create('schedule_overrides', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_schedule_id');
            $table->date('override_date');
            $table->enum('status', ['swapped', 'cancelled', 'moved']);
            $table->unsignedBigInteger('swapped_with_schedule_id')->nullable();
            $table->date('new_date')->nullable();
            $table->time('new_time')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign keys pointing to courses table
            $table->foreign('original_schedule_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('swapped_with_schedule_id')->references('id')->on('courses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_overrides');
    }
};
