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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('grade_id')->nullable()->constrained();
        });

        Schema::create('grade_subject_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->cascadeOnDelete();
            $table->unique(['grade_id', 'subject_id', 'teacher_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_subject_teacher');
        Schema::table('students', function (Blueprint $table) {
            $table->dropConstrainedForeignId('grade_id');
        });
        Schema::dropIfExists('grades');
    }
};
