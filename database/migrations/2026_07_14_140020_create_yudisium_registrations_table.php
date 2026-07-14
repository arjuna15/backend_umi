<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('yudisium_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->boolean('is_free_billing')->default(false);
            $table->boolean('is_free_library')->default(false);
            $table->text('thesis_title');
            $table->decimal('gpa', 3, 2);
            $table->string('thesis_file')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('yudisium_registrations'); }
};
