<?php

namespace Tests\Unit;

use App\Game;
use App\Helpers\Constants;
use App\Helpers\ErrorConstants;
use App\User;
use App\UserColor;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Collection;
use Tests\TestCase;

class GameTest extends TestCase
{
    private $players;
    private $count_x = 3;
    private $count_y = 3;

    private $x;
    private $y;
    private $failsCount = 0;

    public function testTwoUsersGame()
    {
        $this->players = [User::find(1), User::find(2)];
        $response = $this->createGame($this->players[0], [$this->players[1]['id']]);

        if (array_key_exists('error', $response)) {
            print_r($response['error']);
            return;
        }

        $game = Game::find($response['game']['id']);

        $userColors = [];
        $userColors[$game->move_order[0]] = UserColor::with('user')->find($game->move_order[0]);
        $userColors[$game->move_order[1]] = UserColor::with('user')->find($game->move_order[1]);
        $currentUserColor = $userColors[$game->move_order[$game->move_index]];

        $question = null;
        $needClick = true;
        $needAnswer = false;
        $winner = null;

        $moveOrder = collect();
        $error = false;


        while (!$winner && !$error && $this->failsCount < 500) {

            if ($needClick) {
                $this->setRandomBox();
                $response = $this->boxClick($game, $currentUserColor, $this->x, $this->y);
                if (array_key_exists('error', $response)) {
                    $handled = $this->tryHandleError($response['error']);
                    if (!$handled) {
                        $error = true;
                    }
                }
                if (array_key_exists('who_moved', $response)) {
                    $moveOrder->push($response['who_moved']);
                }
                if (array_key_exists('who_moves', $response)) {
                    print_r('who_moves: ' . $response['who_moves']['id'] . PHP_EOL);
                    $currentUserColor = $userColors[$response['who_moves']['id']];
                    $needClick = true;
                    $needAnswer = false;
                }
                if (array_key_exists('question', $response)) {
                    print_r('question: ' . $response['question']['id'] . PHP_EOL);
                    $question = $response['question'];
                    $needClick = false;
                    $needAnswer = true;
                }
            } elseif ($needAnswer) {
                if ($question) {
                    foreach ($userColors as $userColor) {
                        $response = $this->questionAnswer($game, $userColor, $question);
                        if (array_key_exists('error', $response)) {
                            $handled = $this->tryHandleError($response['error']);
                            if (!$handled) {
                                $error = true;
                                break;
                            }
                        }
                        if (array_key_exists('who_moved', $response)) {
                            $moveOrder->push($response['who_moved']);
                        }
                        if (array_key_exists('who_moves', $response)) {
                            print_r('who_moves: ' . $response['who_moves']['id'] . PHP_EOL);
                            $currentUserColor = $userColors[$response['who_moves']['id']];
                            $needClick = true;
                            $needAnswer = false;
                            break;
                        }
                        if (array_key_exists('question', $response)) {
                            print_r('question: ' . $response['question']['id'] . PHP_EOL);
                            $question = $response['question'];
                            $needClick = false;
                            $needAnswer = true;
                            break;
                        }
                        if (array_key_exists('winner', $response)) {
                            $winner = $response['winner'];
                            break;
                        }
                    }
                } else {
                    print_r('no question');
                    break;
                }
            } else {
                print_r('no click or answer');
                break;
            }
        }
        if ($winner) {
            print_r('winner: ' . $winner['id'] . PHP_EOL);
        }
        print_r($moveOrder->groupBy('id')->map(function (Collection $state) {
            return $state->count();
        }));

        print_r($moveOrder->take(-10)->pluck('id')->toArray());
        print_r('iterations: ' . $this->failsCount . PHP_EOL);
        print_r('game_id: ' . $game->id . PHP_EOL);
    }

    private function createGame(User $user, $players)
    {
        $lastGame = Game::orderBy('created_at', 'desc')->first();
        return $this->actingAs($user)->json('POST', '/api/games',
            [
                'title' => 'Game №' . ($lastGame->id + 1),
                'count_x' => $this->count_x,
                'count_y' => $this->count_y,
                'mode' => 'base_mode',
                'users' => $players
            ])->assertStatus(200)->decodeResponseJson();
    }

    private function setRandomBox()
    {
        $this->x = $this->getRandomX($this->count_x - 1);
        $this->y = $this->getRandomY($this->count_y - 1);
    }

    private function getRandomX($x)
    {
        return rand(0, $x);
    }

    private function getRandomY($y)
    {
        return rand(0, $y);
    }

    private function boxClick(Game $game, UserColor $userColor, $x, $y)
    {
        return $this->actingAs($userColor->user)->json(
            'POST', '/api/games/' . $game->id . '/base/box/clicked',
            [
                'x' => $x,
                'y' => $y,
                'userColorId' => $userColor->id,
            ])->assertStatus(200)->decodeResponseJson();
    }

    private function tryHandleError($error)
    {
        $this->failsCount++;
        //print_r($error . PHP_EOL);
        switch ($error) {

            case ErrorConstants::ANOTHER_USER_SHOULD_ANSWER:
                {
                    return true;
                    break;
                }

            case ErrorConstants::GAME_HAS_FINISHED:
            case ErrorConstants::GAME_HAS_ACTIVE_QUESTION:
            case ErrorConstants::NO_USER_MOVE_EXISTS:
            case ErrorConstants::ANOTHER_USER_SHOULD_MOVE:
            case ErrorConstants::GAME_DONT_HAVE_ACTIVE_QUESTION:
            case ErrorConstants::USER_ALREADY_ANSWERED_TO_QUESTION:
            case ErrorConstants::NO_COMPETITIVE_BOX_EXISTS:
            case ErrorConstants::NO_GAME_STAGE_FOUND:
            case ErrorConstants::GAME_VALIDATION_ERRORS:
            case ErrorConstants::GAME_NOT_FOUND:
            case ErrorConstants::QUESTION_NOT_FOUND:
            case ErrorConstants::USER_NOT_FOUND:
                {
                    return false;
                    break;
                }
            case ErrorConstants::BOX_IS_OWNED:
                {
                    $this->setRandomBox();
                    return true;
                    break;
                }

            case Constants::GAME_STAGE_1 . ':' . ErrorConstants::USER_CANNOT_SET_BASE:
                {
                    $this->setRandomBox();
                    return true;
                    break;
                }

            case Constants::GAME_STAGE_1 . ':' . ErrorConstants::NO_USER_MOVE_EXISTS:
            case Constants::GAME_STAGE_1 . ':' . ErrorConstants::USER_ALREADY_SET_BASE:
                {
                    return false;
                    break;
                }

            case Constants::GAME_STAGE_2 . ':' . ErrorConstants::USER_CANNOT_MOVE_TO_BOX:
                {
                    $this->setRandomBox();
                    return true;
                    break;
                }

            case Constants::GAME_STAGE_2 . ':' . ErrorConstants::GAME_NOT_ALL_USERS_ANSWERED:
                {
                    return true;
                    break;
                }

            case Constants::GAME_STAGE_2 . ':' . ErrorConstants::HIDDEN_QUESTION:
            case Constants::GAME_STAGE_2 . ':' . ErrorConstants::CANNOT_DELETE_BOX:
            case Constants::GAME_STAGE_2 . ':' . ErrorConstants::NO_USER_MOVE_EXISTS:
            case Constants::GAME_STAGE_2 . ':' . ErrorConstants::NO_QUESTIONS_LEFT:
            case Constants::GAME_STAGE_2 . ':' . ErrorConstants::USER_TRIES_TO_REMOVE_BASE:
                {
                    return false;
                    break;
                }

            case Constants::GAME_STAGE_3 . ':' . ErrorConstants::GAME_NOT_ALL_USERS_ANSWERED:
                {
                    return true;
                    break;
                }

            case Constants::GAME_STAGE_3 . ':' . ErrorConstants::COMPETITIVE_WINNER_NOT_FOUND:
            case Constants::GAME_STAGE_3 . ':' . ErrorConstants::HIDDEN_QUESTION:
            case Constants::GAME_STAGE_3 . ':' . ErrorConstants::CANNOT_DELETE_BOX:
            case Constants::GAME_STAGE_3 . ':' . ErrorConstants::NO_USER_MOVE_EXISTS:
            case Constants::GAME_STAGE_3 . ':' . ErrorConstants::NO_QUESTIONS_LEFT:
                {
                    return false;
                    break;
                }

            case Constants::GAME_STAGE_4 . ':' . ErrorConstants::USER_CANNOT_ATTACK_OWN_BOX:
            case Constants::GAME_STAGE_4 . ':' . ErrorConstants::USER_CANNOT_MOVE_TO_BOX:
                {
                    $this->setRandomBox();
                    return true;
                    break;
                }

            case Constants::GAME_STAGE_4 . ':' . ErrorConstants::GAME_NOT_ALL_USERS_ANSWERED:
                {
                    return true;
                    break;
                }

            case Constants::GAME_STAGE_4 . ':' . ErrorConstants::COMPETITIVE_WINNER_BOX_NOT_FOUND:
            case Constants::GAME_STAGE_4 . ':' . ErrorConstants::COMPETITIVE_WINNER_NOT_FOUND:
            case Constants::GAME_STAGE_4 . ':' . ErrorConstants::HIDDEN_QUESTION:
            case Constants::GAME_STAGE_4 . ':' . ErrorConstants::CANNOT_DELETE_BOX:
            case Constants::GAME_STAGE_4 . ':' . ErrorConstants::NO_USER_MOVE_EXISTS:
            case Constants::GAME_STAGE_4 . ':' . ErrorConstants::NO_QUESTIONS_LEFT:
            case Constants::GAME_STAGE_4 . ':' . ErrorConstants::UNKNOWN_ERROR:
                {
                    return false;
                    break;
                }
            default:
                {
                    return false;
                    break;
                }
        }
    }

    private function questionAnswer(Game $game, UserColor $userColor, $question)
    {
        if ($userColor->user->id === $this->players[0]->id) {
            $answer = $this->getRandomX(3);
        } else {
            $answer = $this->getRandomX(3);
        }

        return $this->actingAs($userColor->user)->json(
            'POST', '/api/games/' . $game->id . '/base/user/answered',
            [
                'userAnswer' => $answer,
                'userColorId' => $userColor->id,
                'questionId' => $question['id'],
            ])->assertStatus(200)->decodeResponseJson();
    }

    protected function setUp()
    {
        parent::setUp();
        $this->withoutMiddleware([
            ThrottleRequests::class,
        ]);
        $this->withoutEvents();
    }
}
