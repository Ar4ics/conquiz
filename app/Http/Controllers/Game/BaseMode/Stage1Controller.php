<?php

namespace App\Http\Controllers\Game\BaseMode;


use App\Box;
use App\Events\BaseCreated;
use App\Events\UserColorsChanged;
use App\Events\WhoMoves;
use App\Game;
use App\Helpers\Constants;
use App\Helpers\ErrorConstants;
use App\UserColor;

class Stage1Controller
{
    public static function boxClicked(Game $game, $x, $y, UserColor $userColor)
    {
        if (!($userColor->base_box_id)) {

            $rearBox = Box::whereGameId($game->id)->whereIn('x', [$x, $x + 1, $x - 1])->whereIn('y', [$y, $y + 1, $y - 1])->first();

            if ($rearBox) {
                return [
                    'error' => Constants::GAME_STAGE_1 . ':' . ErrorConstants::USER_CANNOT_SET_BASE
                ];
            }

            $box = Box::create([
                'x' => $x,
                'y' => $y,
                'user_color_id' => $userColor->id,
                'cost' => 1000,
                'game_id' => $game->id
            ]);
            $userColor->base_box_id = $box->id;
            $userColor->score += 1000;
            $userColor->save();
            $game->move_index++;

            $who_moves = $game->getMovingUserColor();

            if (!$who_moves) {
                $game->shuffleUserColors();
                $game->stage = Constants::GAME_STAGE_2;
                $who_moves = $game->getMovingUserColor();

                if (!$who_moves) {
                    return [
                        'error' => Constants::GAME_STAGE_1 . ':' . ErrorConstants::NO_USER_MOVE_EXISTS,
                    ];
                }
            }
            $game->save();

            broadcast(new BaseCreated($box));
            broadcast(new WhoMoves($who_moves, $game->id));
            broadcast(new UserColorsChanged($game->id));

            return response()->json(['who_moves' => $who_moves]);
        } else {
            return [
                'error' => Constants::GAME_STAGE_1 . ':' . ErrorConstants::USER_ALREADY_SET_BASE
            ];
        }
    }
}