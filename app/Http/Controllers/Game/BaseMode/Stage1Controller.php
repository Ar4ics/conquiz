<?php

namespace App\Http\Controllers\Game\BaseMode;

use App;
use App\Box;
use App\Events\AnswersResults;
use App\Events\BaseCreated;
use App\Events\BoxClicked;
use App\Events\NewQuestion;
use App\Events\UserColorsChanged;
use App\Events\WhoMoves;
use App\Game;
use App\Http\Controllers\Game\Helpers;
use App\Question;
use App\UserColor;
use Carbon\Carbon;
use Exception;

class Stage1Controller
{
    public static function boxClicked(Game $game, $x, $y, UserColor $userColor)
    {
        if ($userColor->base_box_id) {

            $rearBox = Helpers::getRearBox($userColor, $x, $y);

            if (!$rearBox) {
                return [
                    'error' => 'Этап 1. Вы не можете сходить на это поле'
                ];
            }

            $box = Box::create(['x' => $x, 'y' => $y, 'user_color_id' => $userColor->id, 'game_id' => $game->id]);
            broadcast(new BoxClicked($box));

            $game->move_index++;
            $who_moves = $game->getMovingUserColor();

            if ($who_moves) {
                $game->save();
                broadcast(new WhoMoves($who_moves, $game->id));
            } else {
                $question = Helpers::getQuestion($game, false);
                if (!$question) {
                    return [
                        'error' => 'В игре закончились вопросы'
                    ];
                } else {
                    UserColor::whereGameId($game->id)->update(['has_answered' => false]);

                    $game->current_question_id = $question->id;
                    $game->questioned_at = Carbon::now();
                    $game->shuffleUserColors();

                    broadcast(new NewQuestion($question, $game->id));
                }
            }
        } else {

//            $rearBox = Box::whereGameId($game->id)->whereIn('x', [$x, $x + 1, $x - 1])->whereIn('y', [$y, $y + 1, $y - 1])->first();
//
//            if ($rearBox) {
//                return [
//                    'error' => 'Этап 1. Вы не можете поставить базу на это поле'
//                ];
//            }

            $box = Box::create(['x' => $x, 'y' => $y, 'user_color_id' => $userColor->id, 'cost' => 1000, 'game_id' => $game->id]);
            $userColor->base_box_id = $box->id;
            $userColor->score += 1000;
            $userColor->save();
            $game->move_index++;

            $who_moves = $game->getMovingUserColor();

            if ($who_moves) {
                $game->save();
            } else {
                $game->shuffleUserColors();
                $who_moves = $game->getMovingUserColor();
            }

            broadcast(new BaseCreated($box));
            broadcast(new WhoMoves($who_moves, $game->id));
            broadcast(new UserColorsChanged($game->id));
        }
        return response()->json([]);
    }

    public static function userAnswered(Game $game)
    {
        if (!$game->allUserColorsAnswered()) {
            return response()->json([]);
        }

        $question = Question::find($game->current_question_id);

        if ($question->is_hidden) {
            return ['error' => 'Скрытый вопрос'];
        }
        /** @var Carbon $questionedAt */
        $questionedAt = $game->questioned_at;

        $results = collect();
        $deleted = collect();
        $userQuestions = Helpers::getUserQuestions($game);

        foreach ($userQuestions as $userQuestion) {
            $userQuestion->is_correct = $question->correct === $userQuestion->answer;
            $userColor = $userQuestion->user_color;
            if (!($userQuestion->is_correct)) {
                $deletedBox = Box::where('user_color_id', $userColor->id)
                    ->orderBy('created_at', 'desc')->first();
                $deleted->push($deletedBox);
                try {
                    $deletedBox->delete();
                } catch (Exception $exception) {
                    return ['error' => 'Этап 1. Ошибка при удалении квадрата'];
                }
            } else {
                $userColor->score += 200;
            }
            /** @var Carbon $answeredAt */
            $answeredAt = $userQuestion->answered_at;

            $results->push([
                'user_name' => $userColor->user->name,
                'answer' => $userQuestion->answer,
                'is_correct' => $userQuestion->is_correct,
                'time' => $answeredAt->diff($questionedAt)->format('%s.%f')
            ]);
            $userColor->save();
            $userQuestion->save();
        }

        $boxesFilled = Box::where('game_id', '=', $game->id)->count();
        $boxesLeft = $game->count_x * $game->count_y - $boxesFilled;

        if ($boxesLeft === 0) {
            $game->stage1_has_finished = true;
            $game->stage2_has_finished = true;
        } else if ($boxesLeft < $game->user_colors->count()) {
            $game->stage1_has_finished = true;
        }
        $game->current_question_id = null;
        $game->shuffleUserColors();
        $who_moves = $game->getMovingUserColor();

        if (!$who_moves) {
            return ['error' => 'Этап 1. Нет хода игрока'];
        }

        broadcast(new AnswersResults($results, $deleted, $question->correct, $game->id));
        broadcast(new WhoMoves($who_moves, $game->id));
        broadcast(new UserColorsChanged($game->id));

        return response()->json([]);
    }
}