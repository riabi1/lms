<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Supprimer la colonne category_id et sa clé étrangère
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Restaurer la colonne category_id si rollback
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
        });
    }
};