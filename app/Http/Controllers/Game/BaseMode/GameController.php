<?php

namespace App\Http\Controllers\Game\BaseMode;

use App;
use App\Box;
use App\Game;
use App\Helpers\Constants;
use App\Helpers\ErrorConstants;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Game\Helpers;
use App\Question;
use App\UserColor;
use App\UserQuestion;
use Illuminate\Http\Request;
use Validator;

class GameController extends Controller
{
    public function boxClicked(Request $request, $id)
    {
        $game = Game::find($id);

        if (!$game) {
            return [
                'error' => ErrorConstants::GAME_NOT_FOUND,
            ];
        }

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

        $data = $request->all();

        $validator = Validator::make($data, [
            'x' => 'required|integer',
            'y' => 'required|integer',
            'userColorId' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return [
                'error' => ErrorConstants::GAME_VALIDATION_ERRORS,
            ];
        }

        $userColor = UserColor::find($data['userColorId']);

        if (!$userColor || ($userColor->game_id !== $game->id)) {
            return [
                'error' => ErrorConstants::USER_NOT_FOUND,
            ];
        }

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

        $x = $data['x'];
        $y = $data['y'];

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
            return Stage3Controller::boxClicked($game, $x, $y, $userColor);
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

    public function userAnswered(Request $request, $id)
    {
        $game = Game::find($id);

        if (!$game) {
            return [
                'error' => ErrorConstants::GAME_NOT_FOUND,
            ];
        }

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

        $data = $request->all();

        $validator = Validator::make($data, [
            'userAnswer' => 'required|integer',
            'userColorId' => 'required|integer',
            'questionId' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return [
                'error' => ErrorConstants::GAME_VALIDATION_ERRORS,
            ];
        }

        $userColor = UserColor::find($data['userColorId']);
        $question = Question::find($data['questionId']);

        if (!$userColor || ($userColor->game_id !== $game->id)) {
            return [
                'error' => ErrorConstants::USER_NOT_FOUND,
            ];
        }

        if (!$question) {
            return [
                'error' => ErrorConstants::QUESTION_NOT_FOUND,
            ];
        }

        if (UserQuestion::where('question_id', '=', $question->id)
            ->where('user_color_id', '=', $userColor->id)->first()) {
            return [
                'error' => ErrorConstants::USER_ALREADY_ANSWERED_TO_QUESTION,
            ];
        }

        $userAnswer = $data['userAnswer'];

        if ($game->stage === Constants::GAME_STAGE_2) {
            Helpers::createUserQuestion($userColor, $question, $userAnswer);

            return Stage2Controller::userAnswered($game);
        }

        if (!$game->competitive_box) {
            return [
                'error' => ErrorConstants::NO_COMPETITIVE_BOX_EXISTS,
            ];
        }

        Helpers::createUserQuestion($userColor, $question, $userAnswer);

        if ($game->stage === Constants::GAME_STAGE_3) {
            return Stage3Controller::userAnswered($game);
        }

        if (!in_array($userColor->id, $game->competitive_box->competitors)) {
            return [
                'error' => ErrorConstants::ANOTHER_USER_SHOULD_ANSWER,
            ];
        }

        if ($game->stage === Constants::GAME_STAGE_4) {
            return Stage4Controller::userAnswered($game);
        } else {
            return [
                'error' => ErrorConstants::NO_GAME_STAGE_FOUND,
            ];
        }
    }
}