<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nip')->unique();
            $table->string('name');
            $table->string('position');
            $table->string('department');
            $table->enum('employment_type', ['pns', 'kontrak', 'honorer']);
            $table->date('join_date');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->decimal('salary', 15, 2)->nullable();
            $table->enum('status', ['active', 'inactive', 'retired'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
