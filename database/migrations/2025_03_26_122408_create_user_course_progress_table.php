<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCourseProgressTable extends Migration
{
    public function up()
    {
        Schema::create('user_course_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('lecture_id');
            $table->boolean('completed')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('lecture_id')->references('id')->on('course_lectures')->onDelete('cascade'); // Changement ici
            $table->unique(['user_id', 'course_id', 'lecture_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_course_progress');
    }
}