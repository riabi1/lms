<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateInstructorIdForeignKeyInCouponsTable extends Migration
{
    public function up()
    {
        Schema::table('coupons', function (Blueprint $table) {
            // Supprimer l’ancienne contrainte qui référence "users"
            $table->dropForeign(['instructor_id']);
            // Ajouter la nouvelle contrainte qui référence "instructors"
            $table->foreign('instructor_id')->references('id')->on('instructors')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('coupons', function (Blueprint $table) {
            // Revenir à l’ancienne contrainte pour "users" dans le rollback
            $table->dropForeign(['instructor_id']);
            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
}