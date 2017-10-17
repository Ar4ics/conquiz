<?php

namespace App\Http\Controllers;

use App;
use App\Box;
use App\Events\AnswersResults;
use App\Events\BoxClicked;
use App\Events\GameCreated;
use App\Events\NewQuestion;
use App\Events\WhoMoves;
use App\Game;
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
        $games = Game::withCount('user_colors')->get();

        $users = User::where('id', '<>', Auth::user()->id)->get();

        return view('games', ['games' => $games, 'users' => $users]);
    }


    public function store()
    {

        $nextQuestionId = rand(1, intdiv(Question::count(), 2));
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
            }])->find($id, ['id', 'title', 'current_question_id', 'count_x', 'count_y']) ?? App::abort(404);

        $boxes = Box::join('user_colors', 'boxes.user_color_id', '=', 'user_colors.id')
            ->where('user_colors.game_id', '=', $id)->get(['x', 'y', 'color']);

        $player = $game->getUserColor(Auth::user()->id) ?? collect();
        $who_moves = UserColor::join('users', 'user_colors.user_id', '=', 'users.id')
                ->where('game_id', $game->id)
                ->where('has_moved', '=', 'false')
                ->first(['user_colors.id', 'user_colors.score', 'users.name']) ?? collect();

        $question = Question::find($game->current_question_id,
                ['id', 'title', 'a', 'b', 'c', 'd']) ?? collect();
        return view('game', [
            'game' => $game,
            'player' => $player,
            'boxes' => $boxes,
            'who_moves' => $who_moves,
            'question' => $question
        ]);
    }

    public function boxClicked($id)
    {
        $x = request('x');
        $y = request('y');
        if (Box::join('user_colors', 'boxes.user_color_id', '=', 'user_colors.id')
            ->where('user_colors.game_id', '=', $id)
            ->where('x', '=', $x)
            ->where('y', '=', $y)->first()) {
            return [
                'error' => 'Это поле занято',
                'code' => 0
            ];
        }
        $userColor = UserColor::find(request('userColorId'));
        $userColor->has_moved = true;
        $userColor->save();
        $box = Box::create(['x' => $x, 'y' => $y, 'user_color_id' => $userColor->id]);
        broadcast(new BoxClicked($box));

        $who_moves = UserColor::join('users', 'user_colors.user_id', '=', 'users.id')
            ->where('game_id', $id)
            ->where('has_moved', '=', 'false')
            ->first(['user_colors.id', 'users.name']);
        if ($who_moves) {
            broadcast(new WhoMoves($who_moves->id, $who_moves->name, $id));
        } else {
            $game = Game::find($id);
            $question = Question::find($game->next_question_id);
            $game->current_question_id = $question->id;
            $game->next_question_id++;
            $game->save();
            broadcast(new NewQuestion($question, $id));
        }
    }

    public function userAnswered($id)
    {
        $userAnswer = request('userAnswer');
        $userColorId = request('userColorId');
        $questionId = request('questionId');
        UserQuestion::create([
            'question_id' => $questionId,
            'user_color_id' => $userColorId,
            'answer' => $userAnswer
        ]);
        $userColor = UserColor::find(request('userColorId'));
        $userColor->has_answered = true;
        $userColor->save();
        $game = Game::find($id);
        if ($game->allUserColorsAnswered()) {
            $userColors = $game->user_colors;
            $results = collect();
            $deleted = collect();
            foreach ($userColors as $userColor) {
                $userQuestion = UserQuestion::where('user_color_id', $userColor->id)
                    ->where('question_id', $game->current_question_id)->first();
                $userQuestion->is_correct = $userQuestion->question->correct === $userQuestion->answer;
                $userQuestion->save();
                if (!($userQuestion->is_correct)) {
                    $deletedBox = Box::where('user_color_id', $userColor->id)
                        ->orderBy('created_at', 'desc')->first();
                    $deleted->push($deletedBox);
                    $deletedBox->delete();
                }
                $results->push([
                    'user_color_id' => $userQuestion->user_color_id,
                    'answer' => $userQuestion->answer,
                    'is_correct' => $userQuestion->is_correct
                ]);
                $userColor->has_answered = false;
                $userColor->has_moved = false;
                $userColor->save();

            }
            $game->current_question_id = null;
            $game->save();
            broadcast(new AnswersResults($results, $deleted, $id));
            $who_moves = UserColor::join('users', 'user_colors.user_id', '=', 'users.id')
                ->where('game_id', $id)
                ->where('has_moved', '=', 'false')
                ->first(['user_colors.id', 'users.name']);
            broadcast(new WhoMoves($who_moves->id, $who_moves->name, $id));

        }

    }

    public function readyForGame($id)
    {


    }
}
