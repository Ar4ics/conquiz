<?php

use App\Question;
use Illuminate\Database\Seeder;

class NewQuestionsTableSeeder extends Seeder
{

    function multi_explode($delimiters, $string)
    {
        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return $launch;
    }


    public function run()
    {
        $json_file = file_get_contents("storage/new_questions.json");
        $json_content = json_decode($json_file, true);
        for ($i = 0; $i < count($json_content); $i++) {
            $q = new Question();
            $questionWithAnswers = $json_content[$i]['q'];
            $correct = $json_content[$i]['a'];

            $pos = strpos($questionWithAnswers, ' 1) ');

            if ($pos === false) {
                $q->title = $questionWithAnswers;
                $q->correct = $correct;
                $q->is_exact_answer = true;
                $q->save();
            } else {
                $question = substr($questionWithAnswers, 0, $pos);
                $answers = substr($questionWithAnswers, $pos + 1, strlen($questionWithAnswers) - ($pos + 1));

//                print_r($question);
//                print_r($answers);

//                print_r($correct);

                $answers_exploded = $this->multi_explode(['1)', '2)', '3)', '4)'], $answers);
                $answers_final = [];
                foreach ($answers_exploded as $a) {
                    if (!(trim($a) === '')) {
                        $answers_final[] = trim($a);
                    }
                }

                $pos_correct = array_search($correct, $answers_final);

//                print_r($pos_correct);

                $q->title = $question;
                $q->answers = $answers_final;
                $q->correct = $pos_correct;
                $q->is_exact_answer = false;
                $q->save();
            }
        }
    }
}
