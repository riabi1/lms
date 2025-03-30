<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_course_progress', function (Blueprint $table) {
            $table->id(); // bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT
            $table->morphs('trackable'); // trackable_id et trackable_type pour la relation polymorphique
            $table->foreignId('course_id')->constrained()->onDelete('cascade'); // Référence à courses
            $table->foreignId('lecture_id')->constrained('course_lectures')->onDelete('cascade'); // Référence à course_lectures
            $table->boolean('completed')->default(false); // tinyint(1) NOT NULL DEFAULT 0
            $table->timestamp('completed_at')->nullable(); // timestamp NULL DEFAULT NULL
            $table->timestamps(); // created_at et updated_at

            // Contrainte unique pour éviter les doublons
            $table->unique(['trackable_id', 'trackable_type', 'course_id', 'lecture_id'], 'user_course_progress_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_course_progress');
    }
};