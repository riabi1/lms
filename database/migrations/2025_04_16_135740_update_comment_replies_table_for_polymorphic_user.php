<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCommentRepliesTableForPolymorphicUser extends Migration
{
    public function up()
    {
        Schema::table('comment_replies', function (Blueprint $table) {
            // Drop existing foreign key constraints if they exist
            if (Schema::hasColumn('comment_replies', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('comment_replies', 'user_type')) {
                $table->dropColumn('user_type');
            }

            // Add polymorphic columns
            $table->unsignedBigInteger('user_id')->nullable()->after('comment_id');
            $table->string('user_type')->nullable()->after('user_id');

            // Ensure comment_id foreign key
            if (Schema::hasColumn('comment_replies', 'comment_id')) {
                $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('comment_replies', function (Blueprint $table) {
            // Drop new columns
            if (Schema::hasColumn('comment_replies', 'user_id')) {
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('comment_replies', 'user_type')) {
                $table->dropColumn('user_type');
            }
        });
    }
}