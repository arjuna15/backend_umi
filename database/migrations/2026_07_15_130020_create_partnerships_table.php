<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partnerships', function (Blueprint $table) {
            $table->id();
            $table->string('partner_name');
            $table->enum('partner_type', ['industri', 'pemerintah', 'universitas', 'ngo', 'lainnya']);
            $table->string('mou_number')->nullable()->unique();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('scope');
            $table->enum('status', ['active', 'expired', 'draft'])->default('draft');
            $table->string('pic_name')->nullable();
            $table->string('pic_phone')->nullable();
            $table->string('document_path')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partnerships');
    }
};
