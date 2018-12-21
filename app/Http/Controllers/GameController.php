<?php

namespace App\Http\Controllers;

use App;
use App\Box;
use App\Events\GameCreated;
use App\Game;
use App\Helpers\Constants;
use App\Helpers\ErrorConstants;
use App\Http\Controllers\Game\Stage1Controller;
use App\Http\Controllers\Game\Stage2Controller;
use App\Http\Controllers\Game\Stage3Controller;
use App\Question;
use App\User;
use App\UserColor;
use App\UserQuestion;
use Auth;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameController extends Controller
{
    public function index()
    {
        $games = Game::with('user_colors.user')->with('winner.user')->orderBy('updated_at', 'desc')->get();

        $users = User::where('id', '<>', Auth::user()->id)->get();

        return view('games', ['games' => $games, 'users' => $users]);
    }


    public function store()
    {
        if (request('mode') === 'classic') {
            $nextQuestionId = Question::whereIsHidden(false)
                ->whereIsExactAnswer(false)
                ->inRandomOrder()
                ->first()->id;
        } else {
            $nextQuestionId = null;
        }
        $game = Game::create([
            'title' => request('title'),
            'next_question_id' => $nextQuestionId,
            'count_x' => request('count_x'),
            'count_y' => request('count_y'),
            'mode' => request('mode'),
            'duration' => request('duration'),
            'stage' => Constants::GAME_STAGE_1,
        ]);


        $colors = ["green", "red", "blue"];
        $users = collect(request('users'));
        $users->prepend(Auth::user()->id);

        if ($users->count() > count($colors) || $users->count() < 2) {
            return [
                'error' => ErrorConstants::GAME_USERS_COUNT_MISMATCH,
            ];
        }

        for ($i = 0; $i < $users->count(); $i++) {
            UserColor::create([
                'user_id' => $users->get($i),
                'color' => $colors[$i],
                'game_id' => $game->id
            ]);
        }

        $game->shuffleUserColors();

        $game->save();

        broadcast(new GameCreated($game));

        return response()->json(['game' => $game]);
    }

    public function getGame($id)
    {
        $game = Game::find($id);
        if (!$game) {
            App:abort(404);
        }
        $userColors = UserColor::whereGameId($game->id)->with([
            'user' => function (BelongsTo $q) {
                $q->select(['id', 'name']);
            },
            'base' => function (BelongsTo $q) {
                $q->select(['id', 'x', 'y']);
            }])->get();

        $competitive_box = $game->competitive_box;

        $boxes = Box::join('user_colors', 'boxes.user_color_id', '=', 'user_colors.id')
            ->where('boxes.game_id', '=', $game->id)->get(['x', 'y', 'color', 'user_color_id', 'cost']);

        $player = $game->getUserColor(Auth::user()->id);
        $whoMoves = $game->getMovingUserColor();

        $field = [];

        for ($y = 0; $y < $game->count_y; $y++) {
            $field[$y] = [];
            for ($x = 0; $x < $game->count_x; $x++) {
                $field[$y][] = [
                    'x' => $x,
                    'y' => $y,
                    'color'=> 'white',
                    'cost'=> 0,
                    'user_color_id'=> 0,
                    'base_guards_count'=> 0
                ];
            }
        }

        $boxes->each(function ($box) use (&$field) {
            $b = &$field[$box->y][$box->x];
            $b['cost'] = $box->cost;
            $b['user_color_id'] = $box->user_color_id;
            $b['color'] = $box->color;
        });

        $userColors->each(function ($uc) use (&$field) {
            if ($uc->base) {
                $b = &$field[$uc->base->y][$uc->base->x];
                $b['base_guards_count'] = $uc->base_guards_count;
            }
        });

        $winner = UserColor::with('user')->find($game->winner_user_color_id);
        $competitors = [];
        if ($competitive_box) {
            $target = &$field[$competitive_box->y][$competitive_box->x];
            $target['color'] = 'grey';
            $competitors = $competitive_box->competitors;
        }

        $question = Question::find($game->current_question_id, ['id', 'title', 'answers', 'is_exact_answer']);
        return view('game', [
            'game' => json_encode($game),
            'player' => json_encode($player),
            'field' => json_encode($field),
            'who_moves' => json_encode($whoMoves),
            'question' => json_encode($question),
            'user_colors' => json_encode($userColors),
            'competitors' => json_encode($competitors),
            'winner' => json_encode($winner),
        ]);
    }

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

        if ($game->stage2_has_finished && (!in_array($userColor->id, $game->competitive_box->competitors))) {
            return [
                'error' => 'Вы не должны отвечать на этот вопрос',
            ];
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
            'answer' => $userAnswer
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
