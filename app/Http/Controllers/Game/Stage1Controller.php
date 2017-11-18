<?php

namespace App\Http\Controllers\Game;

use App;
use App\Box;
use App\Events\AnswersResults;
use App\Events\BoxClicked;
use App\Events\NewQuestion;
use App\Events\WhoMoves;
use App\Events\WinnerFound;
use App\Game;
use App\Question;
use App\UserQuestion;
use App\UserColor;

class Stage1Controller
{
    public static function boxClicked(Game $game, $x, $y, UserColor $userColor)
    {
        $box = Box::create(['x' => $x, 'y' => $y, 'user_color_id' => $userColor->id, 'game_id' => $game->id]);
        broadcast(new BoxClicked($box));

        $game->move_index++;
        $game->save();

        $who_moves = $game->getMovingUserColor();

        if ($who_moves) {
            broadcast(new WhoMoves($who_moves->id, $who_moves->user->name, $game->id));
        } else {
            $question = Question::find($game->next_question_id);
            $game->current_question_id = $question->id;
            $game->save();
            broadcast(new NewQuestion($question, $game->id));
        }
        return response()->json([]);
    }

    public static function userAnswered(Game $game)
    {
        if ($game->allUserColorsAnswered()) {
            /** @var UserQuestion $userQuestion */
            $results = collect();
            $deleted = collect();
            $userQuestions = UserQuestion::join('user_colors', 'user_questions.user_color_id', '=', 'user_colors.id')
                ->where('user_colors.game_id', $game->id)
                ->where('question_id', $game->current_question_id)
                ->get();
            foreach ($userQuestions as $userQuestion) {
                $userQuestion->is_correct = $userQuestion->question->correct === $userQuestion->answer;
                $userQuestion->save();
                $userColor = $userQuestion->user_color;
                if (!($userQuestion->is_correct)) {
                    $deletedBox = Box::where('user_color_id', $userColor->id)
                        ->orderBy('created_at', 'desc')->first();
                    $deleted->push($deletedBox);
                    $deletedBox->delete();
                } else {
                    $userColor->score++;
                }
                $results->push([
                    'user_color_id' => $userQuestion->user_color_id,
                    'answer' => $userQuestion->answer,
                    'is_correct' => $userQuestion->is_correct,
                    'score' => $userColor->score
                ]);
                $userColor->has_answered = false;
                $userColor->save();

            }

            $game->shuffleUserColors();
            $quest = Question::find($game->current_question_id);
            $game->current_question_id = null;
            $game->save();
            broadcast(new AnswersResults($results, $deleted, $quest->correct, $game->id));

            $boxesFilled = Box::where('game_id', '=', $game->id)->count();
            $boxesLeft = $game->count_x * $game->count_y - $boxesFilled;

            if ($boxesLeft == 0) {
                $game->stage1_has_finished = true;
                $game->stage2_has_finished = true;
                $game->save();
            } else if ($boxesLeft < $game->user_colors->count()) {
                $game->stage1_has_finished = true;
                $game->save();
            }

            $ids = UserQuestion::join('user_colors', 'user_questions.user_color_id', '=', 'user_colors.id')
                ->where('user_colors.game_id', $game->id)
                ->get()->pluck('question_id')->unique()->values();
            $nextQuestions = Question::whereNotIn('id', $ids)->get();
            if ($nextQuestions->isEmpty()) {
                $game->noQuestionsLeft();
                broadcast(new WinnerFound($game->winner, $game->id));
            } else {
                $game->next_question_id = $nextQuestions->random()->id;
                $game->save();
                $game->shuffleUserColors();
                $who_moves = $game->getMovingUserColor();
                broadcast(new WhoMoves($who_moves->id, $who_moves->user->name, $game->id));
            }

        }
        return response()->json([]);
    }
}