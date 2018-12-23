<?php

namespace App\Http\Controllers\Game\BaseMode;

use App;
use App\Box;
use App\Events\AnswersResults;
use App\Events\BoxClicked;
use App\Events\NewQuestion;
use App\Events\UserColorsChanged;
use App\Events\WhoMoves;
use App\Game;
use App\Helpers\Constants;
use App\Helpers\ErrorConstants;
use App\Http\Controllers\Game\Helpers;
use App\Question;
use App\UserColor;
use Carbon\Carbon;
use Exception;

class Stage2Controller
{
    public static function boxClicked(Game $game, $x, $y, UserColor $userColor)
    {

        $userColorBoxes = Box::whereUserColorId($userColor->id)->get();

        $userColorHasMove = false;

        foreach ($userColorBoxes as $userColorBox) {
            $emptyBox = Helpers::nearEmptyBoxExists($game, $userColorBox->x, $userColorBox->y);
            if ($emptyBox) {
                $userColorHasMove = true;
                break;
            }
        }

        if ($userColorHasMove) {
            $nearOwnBox = Helpers::getNearOwnBox($userColor, $x, $y);

            if (!$nearOwnBox) {
                return [
                    'error' => Constants::GAME_STAGE_2 . ':' . ErrorConstants::USER_CANNOT_MOVE_TO_BOX
                ];
            }
        }

        $box = Box::create([
            'x' => $x,
            'y' => $y,
            'user_color_id' => $userColor->id,
            'game_id' => $game->id
        ]);
        broadcast(new BoxClicked($box));

        $game->move_index++;
        $who_moves = $game->getMovingUserColor();

        if ($who_moves) {
            $game->save();
            broadcast(new WhoMoves($who_moves, $game->id));
            return response()->json(['who_moved' => $userColor, 'who_moves' => $who_moves]);
        } else {
            $question = Helpers::getQuestion($game, false);
            if (!$question) {
                return [
                    'error' => Constants::GAME_STAGE_2 . ':' . ErrorConstants::NO_QUESTIONS_LEFT
                ];
            } else {
                UserColor::whereGameId($game->id)->update(['has_answered' => false]);

                $game->current_question_id = $question->id;
                $game->questioned_at = Carbon::now();
                $game->save();

                broadcast(new NewQuestion($question, $game->id));

                return response()->json(['question' => $question, 'who_moved' => $userColor]);
            }
        }
    }

    public static function userAnswered(Game $game)
    {
        if (!$game->allUserColorsAnswered()) {
            return response()->json([
                'notice' => Constants::GAME_STAGE_2 . ':' . ErrorConstants::GAME_NOT_ALL_USERS_ANSWERED
            ]);
        }

        $question = Question::find($game->current_question_id);

        if ($question->is_hidden) {
            return [
                'error' => Constants::GAME_STAGE_2 . ':' . ErrorConstants::HIDDEN_QUESTION
            ];
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
                    ->orderBy('id', 'desc')->first();

                if ($deletedBox->id === $userColor->base_box_id) {
                    return [
                        'error' => Constants::GAME_STAGE_2 . ':' . ErrorConstants::USER_TRIES_TO_REMOVE_BASE
                    ];
                }

                $deleted->push($deletedBox);
                try {
                    $deletedBox->delete();
                } catch (Exception $exception) {
                    return [
                        'error' => Constants::GAME_STAGE_2 . ':' . ErrorConstants::CANNOT_DELETE_BOX
                    ];
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
            $game->stage = Constants::GAME_STAGE_4;
        } else if ($boxesLeft < $game->user_colors->count()) {
            $game->stage = Constants::GAME_STAGE_3;
        }
        $game->current_question_id = null;
        $game->shuffleUserColors();
        $game->save();
        $who_moves = $game->getMovingUserColor();

        if (!$who_moves) {
            return [
                'error' => Constants::GAME_STAGE_2 . ':' . ErrorConstants::NO_USER_MOVE_EXISTS
            ];
        }

        broadcast(new AnswersResults($results, $deleted, $question->correct, $game->id));
        broadcast(new WhoMoves($who_moves, $game->id));
        broadcast(new UserColorsChanged($game->id));

        return response()->json(['who_moves' => $who_moves]);
    }
}