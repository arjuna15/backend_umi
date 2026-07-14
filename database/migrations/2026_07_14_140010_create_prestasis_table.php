<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('prestasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skpi_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('category', ['akademik', 'non-akademik']);
            $table->enum('level', ['internal', 'regional', 'nasional', 'internasional']);
            $table->string('certificate_path')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('prestasis'); }
};
