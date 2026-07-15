<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('camaba_followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospect_id')->constrained('camaba_prospects')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('method', ['whatsapp', 'phone', 'email', 'visit']);
            $table->text('notes');
            $table->timestamp('followed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('camaba_followups');
    }
};
