<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('camaba_prospects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('school_origin')->nullable();
            $table->string('program_interest')->nullable();
            $table->enum('source', ['website', 'instagram', 'whatsapp', 'pameran', 'referral', 'lainnya'])->default('lainnya');
            $table->enum('status', ['new', 'contacted', 'interested', 'registered', 'lost'])->default('new');
            $table->date('follow_up_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('camaba_prospects');
    }
};
