<?php

namespace App\Http\Controllers;

use App\Events\GameCreated;
use App\Events\GameStarted;
use App\Events\BoxClicked;
use App\Events\UserIsReady;
use Illuminate\Support\Facades\Log;

use App\Game;
use App\User;
use App\UserColor;
use App\Box;
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

        $users = User::where('id', '<>', auth()->user()->id)->get();
        $user = auth()->user();

        return view('games', ['games' => $games, 'users' => $users, 'user' => $user]);
    }


    public function store()
    {
        $game = Game::create(['title' => request('title')]);

        $colors = ["green", "red", "blue"];
        $users = collect(request('users'));
        $users->push(auth()->user()->id);
        $game->users()->attach($users);
        
        for ($i = 0; $i < $game->users->count(); $i++) {
            UserColor::create([
                'user_id' => $game->users[$i]->id, 
                'color' => $colors[$i],
                'game_id' => $game->id
                ]);
        }

        broadcast(new GameCreated($game, $game->users()->count()));

        return $game;
    }

    public function getGame($id)
    {

        $game = Game::findOrFail($id)->load('users');
        $boxes = Box::with(['user_color'])->whereHas("user_color",function($q) use ($id) {
            $q->where("game_id","=", $id);
        })->get();
        $user_color = UserColor::where('user_id', '=', auth()->user()->id)
        ->where('game_id', '=', $game->id)->first();
        if ($user_color == null) {
            $status = 'guest';
            $user_color = '';
        } else {
            $status = 'player';
        }
        //return $game;
        return view('game', [
            'game' => $game, 
            'user_color' => $user_color, 
            'boxes' => $boxes,
            'status' => $status
            ]);
    }

    public function boxClicked($userColorId)
    {
        $x = request('x');
        $y = request('y');
        $box = Box::create(['x' => $x, 'y' => $y, 'user_color_id' => $userColorId]);
        broadcast(new BoxClicked($box));
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
