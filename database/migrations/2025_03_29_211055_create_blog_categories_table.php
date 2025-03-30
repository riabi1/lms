<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id(); // bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT
            $table->string('name'); // varchar(255) NOT NULL
            $table->string('slug')->unique(); // varchar(255) NOT NULL, unique
            $table->text('description')->nullable(); // text DEFAULT NULL
            $table->string('image')->nullable(); // varchar(255) DEFAULT NULL
            $table->timestamps(); // created_at et updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_categories');
    }
};