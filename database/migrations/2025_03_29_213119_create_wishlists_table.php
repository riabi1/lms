<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id(); // bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT
            $table->morphs('trackable'); // trackable_id et trackable_type pour la relation polymorphique
            $table->foreignId('course_id')->constrained()->onDelete('cascade'); // Référence à courses
            $table->timestamps(); // created_at et updated_at

            // Contrainte unique pour éviter les doublons
            $table->unique(['trackable_id', 'trackable_type', 'course_id'], 'wishlists_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};