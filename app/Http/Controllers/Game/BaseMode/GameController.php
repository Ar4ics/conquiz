<?php

namespace App\Http\Controllers\Game\BaseMode;

use App;
use App\Box;
use App\Game;
use App\Helpers\Constants;
use App\Helpers\ErrorConstants;
use App\Http\Controllers\Controller;
use App\UserColor;
use App\UserQuestion;
use Carbon\Carbon;

class GameController extends Controller
{
    public function boxClicked($id)
    {
        $game = Game::find($id);
        if ($game->is_finished) {
            return [
                'error' => ErrorConstants::GAME_HAS_FINISHED,
            ];
        }
        if ($game->current_question_id) {
            return [
                'error' => ErrorConstants::GAME_HAS_ACTIVE_QUESTION,
            ];
        }
        $x = request('x');
        $y = request('y');
        $userColor = UserColor::find(request('userColorId'));

        $who_moves = $game->getMovingUserColor();

        if (!$who_moves) {
            return [
                'error' => ErrorConstants::NO_USER_MOVE_EXISTS,
            ];
        }

        if ($userColor->id !== $who_moves->id) {
            return [
                'error' => ErrorConstants::ANOTHER_USER_SHOULD_MOVE,
            ];
        }

        $box = Box::where('game_id', '=', $game->id)
            ->where('x', '=', $x)
            ->where('y', '=', $y)->first();

        if ($game->stage === Constants::GAME_STAGE_4) {
            return Stage4Controller::boxClicked($game, $x, $y, $userColor, $box);
        }

        if ($box) {
            return [
                'error' => ErrorConstants::BOX_IS_OWNED,
            ];
        }

        if ($game->stage === Constants::GAME_STAGE_3) {
            return Stage3Controller::boxClicked($game, $x, $y);
        }

        if ($game->stage === Constants::GAME_STAGE_2) {
            return Stage2Controller::boxClicked($game, $x, $y, $userColor);
        }

        if ($game->stage === Constants::GAME_STAGE_1) {
            return Stage1Controller::boxClicked($game, $x, $y, $userColor);
        } else {
            return [
                'error' => ErrorConstants::NO_GAME_STAGE_FOUND,
            ];
        }
    }

    public function userAnswered($id)
    {
        $game = Game::find($id);
        if ($game->is_finished) {
            return [
                'error' => ErrorConstants::GAME_HAS_FINISHED,
            ];
        }

        if (!$game->current_question_id) {
            return [
                'error' => ErrorConstants::GAME_DONT_HAVE_ACTIVE_QUESTION,
            ];
        }

        $userAnswer = request('userAnswer');
        $userColorId = request('userColorId');
        $questionId = request('questionId');
        $userColor = UserColor::find(request('userColorId'));

        if (UserQuestion::where('question_id', '=', $questionId)
            ->where('user_color_id', '=', $userColorId)->first()) {
            return [
                'error' => ErrorConstants::USER_ALREADY_ANSWERED_TO_QUESTION,
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

        if ($game->stage === Constants::GAME_STAGE_2) {
            return Stage2Controller::userAnswered($game);
        }

        if (!$game->competitive_box || !$game->competitive_box->competitors) {
            return [
                'error' => ErrorConstants::NO_COMPETITIVE_BOX_EXISTS,
            ];
        }
        if (!in_array($userColor->id, $game->competitive_box->competitors)) {
            return [
                'error' => ErrorConstants::ANOTHER_USER_SHOULD_ANSWER,
            ];
        }

        if ($game->stage === Constants::GAME_STAGE_4) {
            return Stage4Controller::userAnswered($game);
        }

        if ($game->stage === Constants::GAME_STAGE_3) {
            return Stage3Controller::userAnswered($game);
        } else {
            return [
                'error' => ErrorConstants::NO_GAME_STAGE_FOUND,
            ];
        }
    }
}