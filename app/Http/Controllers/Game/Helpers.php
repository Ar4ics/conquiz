<?php


namespace App\Http\Controllers\Game;

use App\Box;
use App\CompetitiveBox;
use App\Events\UserColorLoss;
use App\Game;
use App\Question;
use App\UserColor;
use App\UserQuestion;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Log;

class Helpers
{
    /**
     * @param  Game $game
     * @param  bool $isExactQuestion
     * @return Question|null
     */
    public static function getQuestion($game, $isExactQuestion)
    {
        $ids = UserQuestion::join('user_colors', 'user_questions.user_color_id', '=', 'user_colors.id')
            ->where('user_colors.game_id', $game->id)
            ->get()->pluck('question_id')->unique()->values();
        $question = Question::whereIsHidden(false)->whereIsExactAnswer($isExactQuestion)->whereNotIn('id', $ids)->inRandomOrder()->first();
        return $question;
    }

    /**
     * @param  UserColor $userColor
     * @param  Question $question
     * @param  integer $userAnswer
     * @return void
     */
    public static function createUserQuestion($userColor, $question, $userAnswer)
    {
        UserQuestion::create([
            'question_id' => $question->id,
            'user_color_id' => $userColor->id,
            'answer' => $userAnswer,
            'answered_at' => Carbon::now()
        ]);
        $userColor->has_answered = true;
        $userColor->save();
    }

    /**
     * @param  Game $game
     * @return Collection|UserQuestion[]
     */
    public static function getUserQuestions($game)
    {
        $userQuestions = UserQuestion::join('user_colors', 'user_questions.user_color_id', '=', 'user_colors.id')
            ->where('user_colors.game_id', $game->id)
            ->where('question_id', $game->current_question_id)
            ->orderBy('user_questions.answered_at')
            ->get();
        Log::debug($userQuestions);
        return $userQuestions;
    }

    /**
     * @param  UserColor $userColor
     * @param  integer $x
     * @param  integer $y
     * @return Box|null
     */
    public static function getNearOwnBox($userColor, $x, $y)
    {
        $rearBox = Box::where(function (Builder $query) use ($userColor, $x, $y) {
            $query->where('user_color_id', $userColor->id)
                ->where('x', '=', $x - 1)
                ->where('y', '=', $y);
        })->orWhere(function (Builder $query) use ($userColor, $x, $y) {
            $query->where('user_color_id', $userColor->id)
                ->where('x', '=', $x)
                ->where('y', '=', $y - 1);
        })->orWhere(function (Builder $query) use ($userColor, $x, $y) {
            $query->where('user_color_id', $userColor->id)
                ->where('x', '=', $x + 1)
                ->where('y', '=', $y);
        })->orWhere(function (Builder $query) use ($userColor, $x, $y) {
            $query->where('user_color_id', $userColor->id)
                ->where('x', '=', $x)
                ->where('y', '=', $y + 1);
        })->first();
        return $rearBox;
    }


    /**
     * @param  Game $game
     * @param  integer $x
     * @param  integer $y
     * @return Box|null
     */
    public static function getNearBox($game, $x, $y)
    {
        $rearBox = Box::whereGameId($game->id)->where(function (Builder $query) use ($x, $y) {
            $query->where(function (Builder $query) use ($x, $y) {
                $query
                    ->where('x', '=', $x - 1)
                    ->where('y', '=', $y);
            })->orWhere(function (Builder $query) use ($x, $y) {
                $query
                    ->where('x', '=', $x)
                    ->where('y', '=', $y - 1);
            })->orWhere(function (Builder $query) use ($x, $y) {
                $query
                    ->where('x', '=', $x + 1)
                    ->where('y', '=', $y);
            })->orWhere(function (Builder $query) use ($x, $y) {
                $query
                    ->where('x', '=', $x)
                    ->where('y', '=', $y + 1);
            });
        })->first();
        return $rearBox;
    }

    /**
     * @param  Game $game
     * @param  integer $x
     * @param  integer $y
     * @return boolean
     */
    public static function nearEmptyBoxExists($game, $x, $y)
    {
        if ($x - 1 >= 0) {
            $box = Box::whereGameId($game->id)->where('x', '=', $x - 1)->where('y', '=', $y)->first();
            if (!$box) {
                return true;
            }
        }
        if ($y - 1 >= 0) {
            $box = Box::whereGameId($game->id)->where('x', '=', $x)->where('y', '=', $y - 1)->first();
            if (!$box) {
                return true;
            }
        }
        if ($x + 1 < $game->count_x) {
            $box = Box::whereGameId($game->id)->where('x', '=', $x + 1)->where('y', '=', $y)->first();
            if (!$box) {
                return true;
            }
        }

        if ($y + 1 < $game->count_y) {
            $box = Box::whereGameId($game->id)->where('x', '=', $x)->where('y', '=', $y + 1)->first();
            if (!$box) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  UserColor $winnerUserColor
     * @param  CompetitiveBox $cb
     * @param  Game $game
     * @return Box|null
     * @throws Exception
     */
    public static function findCompetitiveWinnerBox($winnerUserColor, $cb, $game)
    {
        $targetBox = Box::where('x', $cb->x)->where('y', $cb->y)
            ->where('game_id', $game->id)->first();
        if ($winnerUserColor->id === $cb->competitors[0]) {

            $lossUserColor = UserColor::find($cb->competitors[1]);

            if ($lossUserColor->base_box_id === $targetBox->id) {
                $lossUserColor->base_guards_count -= 1;

                if ($lossUserColor->base_guards_count === 0) {
                    $lossUserColor->had_lost = true;


                    $lostUserBoxes = Box::whereUserColorId($lossUserColor->id)->get();

                    foreach ($lostUserBoxes as $lostUserBox) {
                        $lossUserColor->score -= $lostUserBox->cost;
                        $winnerUserColor->score += $lostUserBox->cost;
                        $lostUserBox->user_color_id = $winnerUserColor->id;
                        $lostUserBox->save();
                    }

                    $targetBox['color'] = $winnerUserColor->color;
                    $targetBox['loss_user_color_id'] = $lossUserColor->id;
                } else {
                    $targetBox['color'] = $lossUserColor->color;
                }

                $targetBox['base'] = [
                    'guards' => $lossUserColor->base_guards_count,
                    'user_name' => $lossUserColor->user->name
                ];

            } else {
                $targetBox->delete();
                $lossUserColor->score -= $targetBox->cost;
                $targetBox = Box::create([
                    'x' => $cb->x,
                    'y' => $cb->y,
                    'cost' => 400,
                    'user_color_id' => $winnerUserColor->id,
                    'game_id' => $game->id
                ]);
                $winnerUserColor->score += 400;
                $targetBox['base'] = null;
                $targetBox['color'] = $winnerUserColor->color;
            }
            $lossUserColor->save();

        } else {
            if (!($winnerUserColor->base_box_id === $targetBox->id)) {
                $targetBox->cost += 100;
                $targetBox->save();
                $winnerUserColor->score += 100;
                $targetBox['base'] = null;
            } else {
                $targetBox['base'] = [
                    'guards' => $winnerUserColor->base_guards_count,
                    'user_name' => $winnerUserColor->user->name
                ];
            }
            $targetBox['color'] = $winnerUserColor->color;
        }
        $winnerUserColor->save();
        return $targetBox;
    }
}