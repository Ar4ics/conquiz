<?php

namespace App\Http\Controllers;

use App\Events\GameCreated;
use App\Events\GameStarted;
use App\Events\BoxClicked;
use App\Events\UserIsReady;
use Illuminate\Support\Facades\Log;

use App\Game;
use App\User;
use Illuminate\Http\Request;

class GameController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $games = Game::withCount('users')->get();

        //return $groups[0];

        $users = User::where('id', '<>', auth()->user()->id)->whereNull('game_id')->get();
        $user = auth()->user();

        return view('games', ['games' => $games, 'users' => $users, 'user' => $user]);
    }


    public function store()
    {
        $game = Game::create(['title' => request('title')]);

        $users = collect(request('users'));
        $users->push(auth()->user()->id);

        $game->users()->attach($users);

        broadcast(new GameCreated($game, $game->users()->count()));

        return $game;
    }

    public function getGame($id)
    {
        $game = Game::findOrFail($id)->load('users');
        //return $game;
        return view('game', ['game' => $game]);
    }

    public function boxClicked($id)
    {
        $x = request('x');
        $y = request('y');
        broadcast(new BoxClicked($x, $y, $id));
    }

    public function readyForGame($id)
    {


        $game = Game::findOrFail($id);
        $user = auth()->user();
        $user->game_id = $game->id;
        $user->save();
        //$users = $game->users;
        //Log::info('users' . $game);
        $all_ready = $game->users->every(function ($value, $key) {
            Log::info('user: '. $key . $value);
            return $value->game_id !== null;
        });
        broadcast(new UserIsReady($game));
        if ($all_ready) {
            Log::info('all users ready');
            $game->move_user_id = $game->users()->first()->id;
            $game->save();
            broadcast(new GameStarted($game));
        } else {
            Log::info('some users not ready');
        }
        Log::info('users' . $game);
    }
}
