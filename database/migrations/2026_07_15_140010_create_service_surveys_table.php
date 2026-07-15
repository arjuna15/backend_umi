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
        Schema::create('service_surveys', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['akademik', 'sarpras', 'keuangan']);
            $table->string('aspect');
            $table->decimal('rating', 3, 2);
            $table->integer('respondents_count');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_surveys');
    }
};
