<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompetitiveBoxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('competitive_boxes', function (Blueprint $table) {
            $table->unsignedInteger('game_id')->primary();
            $table->unsignedInteger('x');
            $table->unsignedInteger('y');
            $table->json('competitors');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('competitive_boxes');
    }
}
