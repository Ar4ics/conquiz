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
        $json_file = file_get_contents("storage/questions.json");
        $json_content = json_decode($json_file,true);

        for ($i = 0; $i < count($json_content); $i++)
        {
            $q = new Question();
            $q->title = $json_content[$i]['title'];
            $q->answers = $json_content[$i]['answers'];
            $q->correct = $json_content[$i]['correct'];
            $q->is_exact_answer = false;
            $q->save();
        }
    }
}
