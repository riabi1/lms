<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id(); // bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT
            $table->morphs('reviewable'); // reviewable_id et reviewable_type pour la relation polymorphique
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Référence à users
            $table->foreignId('instructor_id')->nullable()->constrained()->onDelete('set null'); // Référence optionnelle à instructors
            $table->text('comment'); // text NOT NULL
            $table->string('rating'); // varchar(255) NOT NULL
            $table->text('reply')->nullable(); // text DEFAULT NULL
            $table->boolean('status')->default(false); // tinyint(1) NOT NULL DEFAULT 0
            $table->timestamps(); // created_at et updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};