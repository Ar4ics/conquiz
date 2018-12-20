<?php

namespace App\Http\Controllers\Game\BaseMode;

use App;
use App\Box;
use App\Game;
use App\Http\Controllers\Controller;
use App\UserColor;
use App\UserQuestion;
use Carbon\Carbon;

class GameController extends Controller
{
    public function boxClicked($id)
    {
        $game = Game::find($id);
        if ($game->stage3_has_finished) {
            return [
                'error' => 'Игра завершена',
            ];
        }
        if ($game->current_question_id) {
            return [
                'error' => 'Задан вопрос',
            ];
        }
        $x = request('x');
        $y = request('y');
        $userColor = UserColor::find(request('userColorId'));

        $who_moves = $game->getMovingUserColor();

        if (!$who_moves) {
            return [
                'error' => 'Нет хода игрока',
            ];
        }

        if ($userColor->id !== $who_moves->id) {
            return [
                'error' => 'Сейчас ходит ' . $who_moves->user->name,
            ];
        }

        $box = Box::where('game_id', '=', $game->id)
            ->where('x', '=', $x)
            ->where('y', '=', $y)->first();

        if ($game->stage2_has_finished) {
            return Stage3Controller::boxClicked($game, $x, $y, $userColor, $box);
        }

        if ($box) {
            return [
                'error' => 'Это поле занято',
            ];
        }

        if ($game->stage1_has_finished) {
            return Stage2Controller::boxClicked($game, $x, $y);
        }

        return Stage1Controller::boxClicked($game, $x, $y, $userColor);
    }

    public function userAnswered($id)
    {
        $game = Game::find($id);
        if ($game->stage3_has_finished) {
            return [
                'error' => 'Игра завершена',
            ];
        }

        if (!$game->current_question_id) {
            return [
                'error' => 'Вопрос не задан',
            ];
        }

        $userAnswer = request('userAnswer');
        $userColorId = request('userColorId');
        $questionId = request('questionId');
        $userColor = UserColor::find(request('userColorId'));


        if ($game->stage2_has_finished) {

            if (!$game->competitive_box) {
                return [
                    'error' => 'Нет общего квадрата',
                ];
            }
            if (!in_array($userColor->id, $game->competitive_box->competitors)) {
                return [
                    'error' => 'Вы не должны отвечать на этот вопрос',
                ];
            }

        }

        if (UserQuestion::where('question_id', '=', $questionId)
            ->where('user_color_id', '=', $userColorId)->first()) {
            return [
                'error' => 'Вы уже ответили на этот вопрос',
            ];
        }

        UserQuestion::create([
            'question_id' => $questionId,
            'user_color_id' => $userColorId,
            'answer' => $userAnswer,
            'answered_at' => Carbon::now()
        ]);
        $userColor->has_answered = true;
        $userColor->save();
        if ($game->stage2_has_finished) {
            return Stage3Controller::userAnswered($game);
        }

        if ($game->stage1_has_finished) {
            return Stage2Controller::userAnswered($game);
        }

        return Stage1Controller::userAnswered($game);
    }
}