<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // KIP Kuliah, Beasiswa Yayasan, Prestasi
            $table->string('provider'); // Pemerintah (Kemenristek), Yayasan UMIBA, Dll
            $table->enum('discount_type', ['percentage', 'fixed']); // Persentase (misal 100%) atau Nominal (misal Rp 2.500.000)
            $table->decimal('discount_value', 15, 2); // Nilai diskon (100.00 untuk percentage atau nominal UKT)
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarships');
    }
};
