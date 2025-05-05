<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserTypeToCommentReplies extends Migration
{
    public function up()
    {
        Schema::table('comment_replies', function (Blueprint $table) {
            $table->string('user_type')->nullable()->after('user_id');
        });
    }

    public function down()
    {
        Schema::table('comment_replies', function (Blueprint $table) {
            $table->dropColumn('user_type');
        });
    }
}