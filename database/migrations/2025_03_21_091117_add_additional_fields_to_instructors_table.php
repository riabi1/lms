<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalFieldsToInstructorsTable extends Migration
{
    public function up()
    {
        Schema::table('instructors', function (Blueprint $table) {
            $table->text('bio')->nullable()->after('photo'); // Biographie
            $table->text('experience')->nullable()->after('bio'); // Expérience
            $table->text('skills')->nullable()->after('experience'); // Compétences (séparées par des virgules)
            $table->text('education')->nullable()->after('skills'); // Éducation
            $table->string('website', 255)->nullable()->after('education'); // Site web
            $table->string('location', 255)->nullable()->after('website'); // Localisation
        });
    }

    public function down()
    {
        Schema::table('instructors', function (Blueprint $table) {
            $table->dropColumn(['bio', 'experience', 'skills', 'education', 'website', 'location']);
        });
    }
}