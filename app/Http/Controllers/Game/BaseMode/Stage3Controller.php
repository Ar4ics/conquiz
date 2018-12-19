<?php

namespace App\Http\Controllers\Game\BaseMode;

use App;
use App\Box;
use App\CompetitiveBox;
use App\Events\CompetitiveAnswerResults;
use App\Events\CorrectAnswers;
use App\Events\NewExactQuestion;
use App\Events\NewQuestion;
use App\Events\ShowCompetitiveBox;
use App\Events\UserColorsChanged;
use App\Events\WhoAttack;
use App\Events\WhoMoves;
use App\Events\WinnerFound;
use App\Game;
use App\Http\Controllers\Game\Helpers;
use App\Question;
use App\UserColor;
use App\UserQuestion;
use Carbon\Carbon;
use Exception;

class Stage3Controller
{
    public static function boxClicked(Game $game, $x, $y, UserColor $userColor, Box $box)
    {
        if ($box->user_color_id === $userColor->id) {
            return ['error' => 'Этап 3. Вы не можете атаковать поле своего цвета'];
        }

        $rearBox = Helpers::getRearBox($userColor, $x, $y);

        if (!$rearBox) {
            return [
                'error' => 'Этап 3. Вы не можете сходить на это поле',
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
                'error' => 'В игре закончились вопросы'
            ];
        } else {
            UserColor::whereGameId($game->id)->update(['has_answered' => false]);

            $game->current_question_id = $question->id;
            $game->questioned_at = Carbon::now();
            $game->save();

            broadcast(new WhoAttack($competitiveBox, $game->id));
            broadcast(new NewQuestion($question, $game->id));
        }

        return response()->json([]);
    }

    public static function userAnswered(Game $game)
    {
        if (!$game->allCompetitorsAnswered()) {
            return response()->json([]);
        }
        $question = Question::find($game->current_question_id);

        if ($question->is_hidden) {
            return ['error' => 'Скрытый вопрос'];
        }
        /** @var Carbon $questionedAt */
        $questionedAt = $game->questioned_at;
        $userQuestions = Helpers::getUserQuestions($game);

        $results = collect();
        $cb = $game->competitive_box;
        $winnerUserColor = null;
        $targetBox = null;

        if (!$cb || !$cb->competitors) {
            return ['error' => 'Этап 3. Общий квадрат не найден'];
        }

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
                        'error' => 'В игре закончились вопросы'
                    ];
                } else {
                    UserColor::whereGameId($game->id)->update(['has_answered' => false]);

                    $game->current_question_id = $newQuestion->id;
                    $game->save();

                    broadcast(new NewExactQuestion($newQuestion, $game->id));

                    return response()->json([]);
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
                            'error' => 'Этап 3. Ошибка при нахождении победителя'
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
                return ['error' => 'Этап 3. Победитель не выявлен'];
            }

            try {
                $targetBox = Helpers::findCompetitiveWinnerBox($winnerUserColor, $cb, $game);
            } catch (Exception $exception) {
                return [
                    'error' => 'Этап 3. Ошибка при нахождении победителя'
                ];
            }
        }

        try {
            $cb->delete();
        } catch (Exception $exception) {
            return ['error' => 'Этап 3. Ошибка при удалении квадрата'];
        }

        if ($results->isEmpty() || !$targetBox) {
            return [
                'error' => 'Этап 3. Неизвестная ошибка'
            ];
        }

        broadcast(new CompetitiveAnswerResults($results, $targetBox, $question->correct, $winnerUserColor, $game->id));
        if ($game->baseModeWinnerFound()) {
            $winner = UserColor::with('user')->find($game->winner_user_color_id);
            broadcast(new WinnerFound($winner, $game->id));
        } else {
            $game->current_question_id = null;
            $game->move_index++;
            $who_moves = $game->getMovingUserColor();
            if (!$who_moves) {
                $game->shuffleUserColors();
                $who_moves = $game->getMovingUserColor();
            } else {
                $game->save();
            }
            broadcast(new WhoMoves($who_moves, $game->id));
        }
        broadcast(new UserColorsChanged($game->id));

        return response()->json([]);
    }
}