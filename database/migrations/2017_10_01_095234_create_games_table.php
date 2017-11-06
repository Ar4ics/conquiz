<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->unsignedInteger('count_x');
            $table->unsignedInteger('count_y');
            $table->boolean('stage1_has_finished')->default(false);
            $table->boolean('stage2_has_finished')->default(false);
            $table->boolean('stage3_has_finished')->default(false);
            $table->unsignedInteger('current_question_id')->nullable();
            $table->unsignedInteger('next_question_id');
            $table->unsignedInteger('winner_user_color_id')->nullable();
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
        Schema::dropIfExists('games');
    }
}
