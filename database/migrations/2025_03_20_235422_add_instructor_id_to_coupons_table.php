<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInstructorIdToCouponsTable extends Migration
{
    public function up()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->unsignedBigInteger('instructor_id')->nullable()->after('id'); // Ajoute la colonne
            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade'); // Clé étrangère vers la table users
        });
    }

    public function down()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
            $table->dropColumn('instructor_id');
        });
    }
}