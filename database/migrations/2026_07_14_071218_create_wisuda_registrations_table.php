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
        Schema::create('wisuda_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('yudisium_registration_id')->constrained('yudisium_registrations')->cascadeOnDelete();
            $table->enum('toga_size', ['S', 'M', 'L', 'XL', 'XXL']);
            $table->enum('status', ['pending', 'confirmed'])->default('pending');
            $table->string('seat_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wisuda_registrations');
    }
};
