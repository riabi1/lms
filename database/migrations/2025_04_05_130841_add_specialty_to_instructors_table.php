<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSpecialtyToInstructorsTable extends Migration
{
    public function up()
    {
        Schema::table('instructors', function (Blueprint $table) {
            $table->string('specialty', 255)->nullable()->after('experience');
        });
    }

    public function down()
    {
        Schema::table('instructors', function (Blueprint $table) {
            $table->dropColumn('specialty');
        });
    }
}