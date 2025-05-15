<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
  {
    Schema::table('reports', function (Blueprint $table) {
      $table->foreignId('report_category_id')->nullable()->constrained()->onDelete('set null');
    });
  }

  public function down()
  {
    Schema::table('reports', function (Blueprint $table) {
      $table->dropForeign(['report_category_id']);
      $table->dropColumn('report_category_id');
    });
  }
};
