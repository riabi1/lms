<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Supprimer instructor_id
            $table->dropForeign(['instructor_id']);
            $table->dropColumn('instructor_id');

            // Ajouter les colonnes polymorphiques
            $table->morphs('courseable'); // courseable_id et courseable_type
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Supprimer les colonnes polymorphiques
            $table->dropMorphs('courseable');

            // Restaurer instructor_id
            $table->foreignId('instructor_id')->constrained()->onDelete('cascade');
        });
    }
};