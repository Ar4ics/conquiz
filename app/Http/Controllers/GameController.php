<?php

namespace App\Http\Controllers;

use App;
use App\Box;
use App\Events\GameCreated;
use App\Game;
use App\Http\Controllers\Game\Stage1Controller;
use App\Http\Controllers\Game\Stage2Controller;
use App\Http\Controllers\Game\Stage3Controller;
use App\Question;
use App\User;
use App\UserColor;
use App\UserQuestion;
use Auth;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $games = Game::withCount('user_colors')->with('winner.user')->get();

        $users = User::where('id', '<>', Auth::user()->id)->get();

        return view('games', ['games' => $games, 'users' => $users]);
    }


    public function store()
    {
        $nextQuestionId = Question::inRandomOrder()->first()->id; // rand(1, intdiv(Question::count(), 3));
        $game = Game::create([
            'title' => request('title'),
            'next_question_id' => $nextQuestionId,
            'count_x' => request('count_x'),
            'count_y' => request('count_y'),
        ]);

        $colors = ["green", "red", "blue"];
        $users = collect(request('users'));
        $users->prepend(Auth::user()->id);

        for ($i = 0; $i < $users->count(); $i++) {
            UserColor::create([
                'user_id' => $users->get($i),
                'color' => $colors[$i],
                'game_id' => $game->id
            ]);
        }

        broadcast(new GameCreated($game, $game->user_colors()->count()));

        return $game;
    }

    public function getGame($id)
    {
        $game = Game::with(['user_colors' => function (HasMany $q) {
                $q->select(['id', 'game_id', 'user_id', 'color', 'score']);
            }, 'user_colors.user' => function (BelongsTo $q) {
                $q->select(['id', 'name']);
            }])->find($id, ['id', 'title', 'current_question_id', 'count_x', 'count_y', 'stage3_has_finished', 'winner_user_color_id'])
            ?? App::abort(404);

        $competitive_box = $game->competitive_box ?? collect();

        $boxes = Box::join('user_colors', 'boxes.user_color_id', '=', 'user_colors.id')
            ->where('boxes.game_id', '=', $game->id)->get(['x', 'y', 'color']);

        $player = $game->getUserColor(Auth::user()->id) ?? collect();
        $who_moves = (!($game->current_question_id) && !($game->stage3_has_finished)) ? UserColor::join('users', 'user_colors.user_id', '=', 'users.id')
            ->where('game_id', $game->id)
            ->sequenced()
            ->first(['user_colors.id', 'user_colors.score', 'users.name']) : collect();
        $question = Question::find($game->current_question_id,
                ['id', 'title', 'answers']) ?? collect();
        return view('game', [
            'game' => $game,
            'player' => $player,
            'boxes' => $boxes,
            'who_moves' => $who_moves,
            'question' => $question,
            'competitive_box' => $competitive_box,
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

        $who_moves = UserColor::join('users', 'user_colors.user_id', '=', 'users.id')
            ->where('game_id', $game->id)
            ->sequenced()
            ->first(['user_colors.id', 'users.name']);

        if ($userColor->id !== $who_moves->id) {
            return [
                'error' => 'Сейчас ходит ' . $who_moves->name,
            ];
        }

        if ($game->stage2_has_finished) {
            return Stage3Controller::boxClicked($game, $x, $y, $userColor);
        }

        if (Box::where('game_id', '=', $game->id)
            ->where('x', '=', $x)
            ->where('y', '=', $y)->first()) {
            return [
                'error' => 'Это поле занято',
            ];
        }

        if ($game->stage1_has_finished) {
            return Stage2Controller::boxClicked($game, $x, $y, $userColor);
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

        $userAnswer = request('userAnswer');
        $userColorId = request('userColorId');
        $questionId = request('questionId');

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
        $userColor = UserColor::find(request('userColorId'));
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
