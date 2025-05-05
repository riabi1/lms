<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToCommentsTable extends Migration
{
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id'); // Ajouter user_id
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->dropColumn('name'); // Supprimer name (optionnel, car on utilisera le nom de l'utilisateur)
            $table->dropColumn('email'); // Supprimer email (optionnel)
        });
    }

    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->string('name')->nullable(); // Restaurer name
            $table->string('email')->nullable(); // Restaurer email
        });
    }
}