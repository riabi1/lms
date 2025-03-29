<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->unsignedBigInteger('user_id'); // Foreign key to users table (student)
            $table->unsignedBigInteger('course_id'); // Foreign key to courses table
            $table->unsignedBigInteger('instructor_id')->nullable(); // Foreign key to instructors table (nullable until answered)
            $table->text('question_text'); // The student's question
            $table->text('answer_text')->nullable(); // The instructor's answer
            $table->enum('status', ['pending', 'answered', 'closed'])->default('pending'); // Status of the question
            $table->timestamps(); // created_at and updated_at

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('instructor_id')->references('id')->on('instructors')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('questions');
    }
}