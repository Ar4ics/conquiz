<?php

namespace App\Http\Controllers\Game\BaseMode;

use App;
use App\Box;
use App\CompetitiveBox;
use App\Events\CompetitiveAnswerResults;
use App\Events\CorrectAnswers;
use App\Events\NewExactQuestion;
use App\Events\NewQuestion;
use App\Events\UserColorsChanged;
use App\Events\WhoAttack;
use App\Events\WhoMoves;
use App\Events\WinnerFound;
use App\Game;
use App\Helpers\Constants;
use App\Helpers\ErrorConstants;
use App\Http\Controllers\Game\Helpers;
use App\Question;
use App\UserColor;
use App\UserQuestion;
use Carbon\Carbon;
use Exception;

class Stage4Controller
{
    public static function boxClicked(Game $game, $x, $y, UserColor $userColor, Box $box)
    {
        if ($box->user_color_id === $userColor->id) {
            return [
                'error' => Constants::GAME_STAGE_4 . ':' . ErrorConstants::USER_CANNOT_ATTACK_OWN_BOX
            ];
        }

        $nearOwnBox = Helpers::getNearOwnBox($userColor, $x, $y);

        if (!$nearOwnBox) {
            return [
                'error' => Constants::GAME_STAGE_4 . ':' . ErrorConstants::USER_CANNOT_MOVE_TO_BOX
            ];
        }

        $competitors = collect();
        $competitors->push($userColor->id);
        $competitors->push($box->user_color_id);
        $competitiveBox = new CompetitiveBox(['x' => $x, 'y' => $y, 'competitors' => $competitors]);
        $game->competitive_box()->save($competitiveBox);

        $question = Helpers::getQuestion($game, false);

        if (!$question) {
            return [
                'error' => Constants::GAME_STAGE_4 . ':' . ErrorConstants::NO_QUESTIONS_LEFT
            ];
        } else {
            UserColor::whereGameId($game->id)->update(['has_answered' => false]);

            $game->current_question_id = $question->id;
            $game->questioned_at = Carbon::now();
            $game->save();

            broadcast(new WhoAttack($competitiveBox, $game->id));
            broadcast(new NewQuestion($question, $game->id));

            return response()->json(['question' => $question]);
        }
    }

    public static function userAnswered(Game $game)
    {
        if (!$game->allCompetitorsAnswered()) {
            return response()->json([
                'notice' => Constants::GAME_STAGE_4 . ':' . ErrorConstants::GAME_NOT_ALL_USERS_ANSWERED
            ]);
        }
        $question = Question::find($game->current_question_id);

        if ($question->is_hidden) {
            return [
                'error' => Constants::GAME_STAGE_4 . ':' . ErrorConstants::HIDDEN_QUESTION
            ];
        }
        /** @var Carbon $questionedAt */
        $questionedAt = $game->questioned_at;
        $userQuestions = Helpers::getUserQuestions($game);

        $results = collect();
        $cb = $game->competitive_box;
        $winnerUserColor = null;
        $targetBox = null;

        if (!$question->is_exact_answer) {

            foreach ($userQuestions as $userQuestion) {
                $userQuestion->is_correct = $question->correct === $userQuestion->answer;
                /** @var Carbon $answeredAt */
                $answeredAt = $userQuestion->answered_at;
                $results->push([
                    'user_name' => $userQuestion->user_color->user->name,
                    'answer' => $userQuestion->answer,
                    'is_correct' => $userQuestion->is_correct,
                    'time' => $answeredAt->diff($questionedAt)->format('%s.%f')
                ]);
                $userQuestion->save();
            }

            $allCompetitorsCorrect = $userQuestions->every(function (UserQuestion $value) {
                return $value->is_correct;
            });

            if ($allCompetitorsCorrect) {
                broadcast(new CorrectAnswers($results, $question->correct, $game->id));

                $newQuestion = Helpers::getQuestion($game, true);

                if (!$newQuestion) {
                    return [
                        'error' => Constants::GAME_STAGE_4 . ':' . ErrorConstants::HIDDEN_QUESTION
                    ];
                } else {
                    UserColor::whereGameId($game->id)->update(['has_answered' => false]);

                    $game->current_question_id = $newQuestion->id;
                    $game->save();

                    broadcast(new NewExactQuestion($newQuestion, $game->id));

                    return response()->json(['question' => $newQuestion]);
                }

            } else {

                /** @var UserQuestion $userQuestion */
                $userQuestion = $userQuestions->first(function (UserQuestion $value) {
                    return $value->is_correct;
                });

                if ($userQuestion) {
                    $winnerUserColor = $userQuestion->user_color;

                    try {
                        $targetBox = Helpers::findCompetitiveWinnerBox($winnerUserColor, $cb, $game);
                    } catch (Exception $exception) {
                        return [
                            'error' => Constants::GAME_STAGE_4 . ':' . ErrorConstants::COMPETITIVE_WINNER_BOX_NOT_FOUND
                        ];
                    }

                } else {
                    $targetBox = Box::where('x', $cb->x)->where('y', $cb->y)
                        ->where('game_id', $game->id)->first();
                    $uc = UserColor::find($targetBox->user_color_id);
                    $targetBox['color'] = $uc->color;
                    if ($uc->base_box_id === $targetBox->id) {
                        $targetBox['base_guards_count'] = $uc->base_guards_count;
                    } else {
                        $targetBox['base_guards_count'] = 0;
                    }
                }
            }

        } else {
            $minDiff = null;
            foreach ($userQuestions as $userQuestion) {
                $userQuestion->is_correct = $question->correct === $userQuestion->answer;
                $diff = abs($question->correct - $userQuestion->answer);
                if ($minDiff === null || $diff < $minDiff) {
                    $minDiff = $diff;
                    $winnerUserColor = $userQuestion->user_color;
                }
                /** @var Carbon $answeredAt */
                $answeredAt = $userQuestion->answered_at;
                $results->push([
                    'user_name' => $userQuestion->user_color->user->name,
                    'answer' => $userQuestion->answer,
                    'is_correct' => $userQuestion->is_correct,
                    'time' => $answeredAt->diff($questionedAt)->format('%s.%f')
                ]);
                $userQuestion->save();
            }

            if (!$winnerUserColor) {
                return [
                    'error' => Constants::GAME_STAGE_4 . ':' . ErrorConstants::COMPETITIVE_WINNER_NOT_FOUND
                ];
            }

            try {
                $targetBox = Helpers::findCompetitiveWinnerBox($winnerUserColor, $cb, $game);
            } catch (Exception $exception) {
                return [
                    'error' => Constants::GAME_STAGE_4 . ':' . ErrorConstants::COMPETITIVE_WINNER_BOX_NOT_FOUND
                ];
            }
        }

        try {
            $cb->delete();
        } catch (Exception $exception) {
            return [
                'error' => Constants::GAME_STAGE_4 . ':' . ErrorConstants::CANNOT_DELETE_BOX
            ];
        }

        if ($results->isEmpty() || !$targetBox) {
            return [
                'error' => Constants::GAME_STAGE_4 . ':' . ErrorConstants::UNKNOWN_ERROR
            ];
        }

        broadcast(new CompetitiveAnswerResults($results, $targetBox, $question->correct, $winnerUserColor, $game->id));
        broadcast(new UserColorsChanged($game->id));
        if ($game->baseModeWinnerFound()) {
            $game->save();
            $winner = UserColor::with('user')->find($game->winner_user_color_id);
            broadcast(new WinnerFound($winner, $game->id));

            return response()->json(['winner' => $winner]);

        } else {
            $game->current_question_id = null;
            $game->move_index++;

            $who_moves = $game->getMovingUserColor();
            if (!$who_moves) {
                $game->shuffleUserColors();
                $who_moves = $game->getMovingUserColor();

                if (!$who_moves) {
                    return [
                        'error' => Constants::GAME_STAGE_4 . ':' . ErrorConstants::NO_USER_MOVE_EXISTS,
                    ];
                }
            }

            $game->save();

            broadcast(new WhoMoves($who_moves, $game->id));

            return response()->json(['who_moves' => $who_moves]);
        }
    }
}