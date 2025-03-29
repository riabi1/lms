<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->unsignedBigInteger('reporter_id'); // Polymorphic ID (user or instructor)
            $table->string('reporter_type'); // Polymorphic type (e.g., 'App\Models\User' or 'App\Models\Instructor')
            $table->unsignedBigInteger('course_id')->nullable(); // Foreign key to courses (optional)
            $table->string('title'); // Short subject of the report
            $table->text('description'); // Detailed report content
            $table->enum('status', ['pending', 'seen', 'resolved'])->default('pending'); // Report status
           
            $table->text('resolution_notes')->nullable(); // Admin's resolution notes
            $table->timestamps(); // created_at and updated_at

            // Foreign key constraints
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('set null');
           

            // Index for polymorphic relationship
            $table->index(['reporter_id', 'reporter_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reports');
    }
}