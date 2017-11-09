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

class Stage3Controller
{
    public static function boxClicked(Game $game, $x, $y, UserColor $userColor)
    {
        $box = Box::where('game_id', '=', $game->id)
            ->where('x', '=', $x)
            ->where('y', '=', $y)->first();
        if ($box->user_color_id === $userColor->id) {
            return [
                'error' => 'Это поле вашего цвета',
            ];
        }

        $rearBox = Box::where(function ($query) use ($userColor, $x, $y) {
            $query->where('user_color_id', $userColor->id)
                ->where('x', '=', $x - 1)
                ->where('y', '=', $y);
        })->orWhere(function ($query) use ($userColor, $x, $y) {
            $query->where('user_color_id', $userColor->id)
                ->where('x', '=', $x)
                ->where('y', '=', $y - 1);
        })->orWhere(function ($query) use ($userColor, $x, $y) {
            $query->where('user_color_id', $userColor->id)
                ->where('x', '=', $x + 1)
                ->where('y', '=', $y);
        })->orWhere(function ($query) use ($userColor, $x, $y) {
            $query->where('user_color_id', $userColor->id)
                ->where('x', '=', $x)
                ->where('y', '=', $y + 1);
        })->first();

        if (!$rearBox) {
            return [
                'error' => 'Вы не можете сходить на это поле',
            ];
        }

        $userColor->down();
        $competitors = collect();
        $competitors->push($userColor->id);
        $competitors->push($box->user_color_id);
        $competitiveBox = new CompetitiveBox(['x' => $x, 'y' => $y, 'competitors' => $competitors]);
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
        if ($game->allCompetitorsAnswered()) {
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
            if ($userQuestion && $userQuestion->user_color_id === $cb->competitors[0]) {
                try {
                    Box::where('x', $cb->x)->where('y', $cb->y)
                        ->where('game_id', $game->id)->delete();
                } catch (\Exception $e) {

                }
                $box = Box::create(['x' => $cb->x, 'y' => $cb->y, 'user_color_id' => $userQuestion->user_color_id, 'game_id' => $game->id]);
                broadcast(new BoxClicked($box));
            } else {
                $box = Box::where('x', $cb->x)->where('y', $cb->y)
                    ->where('game_id', $game->id)->first();
                broadcast(new BoxClicked($box));
            }
            $cb->delete();


            $quest = Question::find($game->current_question_id);
            $game->current_question_id = null;
            $game->save();
            broadcast(new AnswersResults($results, $deleted, $quest->correct, $game->id));


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

                if ($game->winnerFound()) {
                    broadcast(new WinnerFound($game->winner, $game->id));
                } else {
                    $who_moves = UserColor::join('users', 'user_colors.user_id', '=', 'users.id')
                        ->where('game_id', $game->id)
                        ->sequenced()
                        ->first(['user_colors.id', 'users.name']);
                    broadcast(new WhoMoves($who_moves->id, $who_moves->name, $game->id));
                }
            }
        }
        return response()->json([]);
    }
}