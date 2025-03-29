<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversationsTable extends Migration
{
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->unsignedBigInteger('user_id'); // Foreign key to users table
            $table->unsignedBigInteger('instructor_id'); // Foreign key to instructors table
            $table->timestamp('last_message_at')->nullable(); // Time of the last message
            $table->timestamps(); // created_at and updated_at

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('instructor_id')->references('id')->on('instructors')->onDelete('cascade');

            // Unique constraint to prevent duplicate conversations between the same user and instructor
            $table->unique(['user_id', 'instructor_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('conversations');
    }
}