<?php

namespace App\Http\Controllers\Game;

use App;
use App\Box;
use App\CompetitiveBox;
use App\Events\AnswersResults;
use App\Events\BoxClicked;
use App\Events\NewQuestion;
use App\Events\ShowCompetitiveBox;
use App\Events\WhoMoves;
use App\Events\WinnerFound;
use App\Game;
use App\Question;
use App\UserColor;
use App\UserQuestion;

class Stage2Controller
{
    public static function boxClicked(Game $game, $x, $y)
    {
        $competitiveBox = new CompetitiveBox(['x' => $x, 'y' => $y, 'competitors' => $game->user_colors->pluck('id')]);
        $game->competitive_box()->save($competitiveBox);
        broadcast(new ShowCompetitiveBox($competitiveBox->x, $competitiveBox->y, $game->id));
        $question = Question::find($game->next_question_id);
        $game->current_question_id = $question->id;
        $game->save();
        broadcast(new NewQuestion($question, $game->id));
        return response()->json([]);
    }

    public static function userAnswered(Game $game)
    {
        if ($game->allUserColorsAnswered()) {
            /** @var UserQuestion $userQuestion */
            $results = collect();
            $userQuestions = UserQuestion::join('user_colors', 'user_questions.user_color_id', '=', 'user_colors.id')
                ->where('user_colors.game_id', $game->id)
                ->where('question_id', $game->current_question_id)
                ->orderBy('user_questions.created_at')
                ->get();

            foreach ($userQuestions as $userQuestion) {
                $userQuestion->is_correct = $userQuestion->question->correct === $userQuestion->answer;
                $userQuestion->save();
                $userColor = $userQuestion->user_color;
                if ($userQuestion->is_correct) {
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

            $deleted = collect();
            $cb = $game->competitive_box;
            $userQuestion = $userQuestions->first(function ($value) {
                return $value['is_correct'];
            });
            if ($userQuestion) {
                $box = Box::create(['x' => $cb->x, 'y' => $cb->y, 'user_color_id' => $userQuestion->user_color_id, 'game_id' => $game->id]);
                broadcast(new BoxClicked($box));
            } else {
                $deleted->push(['x' => $cb->x, 'y' => $cb->y]);
            }
            $cb->delete();

            $quest = Question::find($game->current_question_id);
            $game->current_question_id = null;
            $game->save();
            broadcast(new AnswersResults($results, $deleted, $quest->correct, $game->id));

            $boxesFilled = Box::where('game_id', '=', $game->id)->count();
            $boxesLeft = $game->count_x * $game->count_y - $boxesFilled;

            if ($boxesLeft == 0) {
                $game->stage2_has_finished = true;
                $game->save();

                if ($game->winnerUserColorWasFound()) {
                    broadcast(new WinnerFound($game->winner, $game->id));
                }
            }

            $ids = UserQuestion::join('user_colors', 'user_questions.user_color_id', '=', 'user_colors.id')
                ->where('user_colors.game_id', $game->id)
                ->get()->pluck('question_id')->unique()->values();
            $nextQuestions = Question::whereIsHidden(false)->whereIsExactAnswer(false)->whereNotIn('id', $ids)->get();
            if ($nextQuestions->isEmpty()) {
                $game->noQuestionsLeft();
                broadcast(new WinnerFound($game->winner, $game->id));
            } else {
                $game->next_question_id = $nextQuestions->random()->id;
                $game->move_index++;
                $game->save();
                $who_moves = $game->getMovingUserColor();
                if (!$who_moves) {
                    $game->shuffleUserColors();
                    $who_moves = $game->getMovingUserColor();
                }
                broadcast(new WhoMoves($who_moves->id, $who_moves->user->name, $game->id));

            }
        }
        return response()->json([]);

    }
}