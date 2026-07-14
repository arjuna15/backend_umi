<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('edom_questions', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->string('category');
            $table->timestamps();
        });
        Schema::create('edom_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('edom_questions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('course_id');
            $table->foreignId('dosen_id')->constrained('users')->onDelete('cascade');
            $table->unsignedTinyInteger('score');
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('edom_answers');
        Schema::dropIfExists('edom_questions');
    }
};
