<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMessageColumnToMessagesTable extends Migration
{
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->text('message')->nullable(); // Ou 'not nullable' selon ton besoin
        });
    }

    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('message');
        });
    }
}