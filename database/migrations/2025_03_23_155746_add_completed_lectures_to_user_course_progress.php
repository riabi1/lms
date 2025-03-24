<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompletedLecturesToUserCourseProgress extends Migration
{
    public function up()
    {
        Schema::table('user_course_progress', function (Blueprint $table) {
            $table->json('completed_lectures')->nullable()->after('progress');
        });
    }

    public function down()
    {
        Schema::table('user_course_progress', function (Blueprint $table) {
            $table->dropColumn('completed_lectures');
        });
    }
}