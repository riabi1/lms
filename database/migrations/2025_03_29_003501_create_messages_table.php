<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->unsignedBigInteger('conversation_id'); // Foreign key to conversations table
            $table->unsignedBigInteger('sender_id'); // Polymorphic ID (user or instructor)
            $table->string('sender_type'); // Polymorphic type (e.g., 'App\Models\User' or 'App\Models\Instructor')
            $table->text('content'); // Message text
            $table->boolean('is_read')->default(false); // Whether the message has been read
            $table->timestamps(); // created_at and updated_at

            // Foreign key constraints
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');

            // Index for polymorphic relationship
            $table->index(['sender_id', 'sender_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
    }
}