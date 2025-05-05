<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('blog_post_id'); // Clé étrangère vers blog_posts
            $table->string('name')->nullable(); // Nom de l'auteur (optionnel)
            $table->string('email')->nullable(); // Email de l'auteur (optionnel)
            $table->text('message'); // Contenu du commentaire
            $table->boolean('approved')->default(false); // Statut d'approbation
            $table->timestamps();

            // Clé étrangère
            $table->foreign('blog_post_id')->references('id')->on('blog_posts')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('comments');
    }
}