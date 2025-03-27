<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColorToCourseNotesTable extends Migration
{
    public function up()
    {
        Schema::table('course_notes', function (Blueprint $table) {
            $table->string('color')->nullable()->after('favorite');
        });
    }

    public function down()
    {
        Schema::table('course_notes', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
}