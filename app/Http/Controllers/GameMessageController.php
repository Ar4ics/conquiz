<?php

namespace App\Http\Controllers;


use App\Events\GameMessageCreated;
use App\GameMessage;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Validator;

class GameMessageController extends Controller
{
    public function index($gameId)
    {
        $messages = GameMessage::whereGameId($gameId)->with('user')->orderBy('created_at')->get()
            ->groupBy('date')
            ->map(function (Collection $collection, $key) {
                return ['date' => $key, 'messages' => $collection];
            })->values();
        return $messages;
    }

    public function store(Request $request, $gameId)
    {
        $user = Auth::user();
        $data = $request->all();

        $data['user_id'] = $user->id;
        $data['game_id'] = $gameId;

        $validation = Validator::make($data, GameMessage::$createRules);

        if ($validation->fails()) {
            return response()->json($validation->errors()->all(), 500);
        }

        $message = GameMessage::create($data);

        $message->load('user');

        broadcast(new GameMessageCreated($message));
        return $message;
    }
}