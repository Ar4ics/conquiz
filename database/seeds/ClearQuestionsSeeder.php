<?php

use Illuminate\Database\Seeder;

class ClearQuestionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('TRUNCATE questions CASCADE');
    }
}
