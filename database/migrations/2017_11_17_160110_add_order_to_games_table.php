<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderToGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->json('move_order')->default("[]");
            $table->unsignedInteger('move_index')->default(0);
        });

        Schema::table('user_colors', function (Blueprint $table) {
            $table->dropColumn('has_moved');
            $table->dropColumn('seq');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('move_order');
            $table->dropColumn('move_index');
        });

        Schema::table('user_colors', function (Blueprint $table) {
            $table->boolean('has_moved')->default(false);
            $table->unsignedInteger('seq')->default(1);
        });
    }
}
