<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToCourseNotesTable extends Migration
{
    public function up()
    {
        Schema::table('course_notes', function (Blueprint $table) {
            $table->string('title')->after('course_id'); // Ajoute la colonne title après course_id
            $table->date('due_date')->nullable()->after('content'); // Ajoute la colonne due_date, nullable
            $table->boolean('favorite')->default(false)->after('due_date'); // Ajoute la colonne favorite avec false par défaut
        });
    }

    public function down()
    {
        Schema::table('course_notes', function (Blueprint $table) {
            $table->dropColumn(['title', 'due_date', 'favorite']); // Supprime les colonnes en cas de rollback
        });
    }
}