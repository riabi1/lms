<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogsTable extends Migration
{
    public function up()
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->unsignedBigInteger('instructor_id'); // Foreign key to instructors table
            $table->string('title'); // Blog post title
            $table->string('slug')->unique(); // URL-friendly slug
            $table->text('content'); // Blog content
            $table->string('image')->nullable(); // Path to featured image
            $table->enum('status', ['draft', 'pending', 'approved'])->default('draft'); // Blog status
            $table->timestamp('approved_at')->nullable(); // When it was approved
            $table->timestamps(); // created_at and updated_at

            // Foreign key constraints
            $table->foreign('instructor_id')->references('id')->on('instructors')->onDelete('cascade');
            
        });
    }

    public function down()
    {
        Schema::dropIfExists('blogs');
    }
}