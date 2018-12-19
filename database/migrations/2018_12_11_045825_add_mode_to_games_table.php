<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddModeToGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->string('mode')->nullable()->default('classic');
            $table->unsignedInteger('duration')->nullable()->default(10);
            $table->unsignedInteger('next_question_id')->nullable()->change();
            $table->timestamp('questioned_at', 6)->nullable();
        });

        Schema::table('user_colors', function (Blueprint $table) {
            $table->unsignedInteger('base_box_id')->nullable();
            $table->unsignedInteger('base_guards_count')->nullable()->default(3);
        });

        Schema::table('user_questions', function (Blueprint $table) {
            $table->timestamp('answered_at', 6)->nullable();
        });

        Schema::table('boxes', function (Blueprint $table) {
            $table->integer('cost')->nullable()->default(200);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->boolean('is_hidden')->nullable()->default(false);
            $table->boolean('is_exact_answer')->nullable()->default(false);
            $table->json('answers')->nullable()->change();
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
            $table->dropColumn('mode');
            $table->dropColumn('duration');
            $table->dropColumn('questioned_at');
        });

        Schema::table('user_colors', function (Blueprint $table) {
            $table->dropColumn('base_box_id');
            $table->dropColumn('base_guards_count');
        });

        Schema::table('user_questions', function (Blueprint $table) {
            $table->dropColumn('answered_at');
        });

        Schema::table('boxes', function (Blueprint $table) {
            $table->dropColumn('cost');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('is_hidden');
            $table->dropColumn('is_exact_answer');
        });
    }
}
