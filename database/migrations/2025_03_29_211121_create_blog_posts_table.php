<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id(); // bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT
            $table->foreignId('instructor_id')->constrained()->onDelete('cascade'); // Référence à instructors
            $table->foreignId('blog_category_id')->constrained()->onDelete('cascade'); // Référence à blog_categories
            $table->string('title'); // varchar(255) NOT NULL
            $table->string('slug')->unique(); // varchar(255) NOT NULL, unique
            $table->text('content'); // text NOT NULL
            $table->string('image')->nullable(); // varchar(255) DEFAULT NULL
            $table->enum('status', ['draft', 'pending', 'approved'])->default('draft'); // enum avec valeur par défaut
            $table->timestamp('approved_at')->nullable(); // timestamp DEFAULT NULL
            $table->timestamps(); // created_at et updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};