<?php

use App\Question;
use Illuminate\Database\Seeder;

class QuestionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //factory(Question::class, 100)->create();
        DB::statement('TRUNCATE questions CASCADE');
        $json_file = file_get_contents("storage/questions.json");
        $json_content = json_decode($json_file,true);

        for ($i = 0; $i < count($json_content); $i++)
        {
            $q = new Question();
            $q->title = $json_content[$i]['title'];
            $correct = $json_content[$i]['answers'][0];
            $answers = $json_content[$i]['answers'];
            shuffle($answers);
            $q->answers = $answers;
            $q->correct = array_search($correct, $answers);
            $q->save();
        }
    }
}
