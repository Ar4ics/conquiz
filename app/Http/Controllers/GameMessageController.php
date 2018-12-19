<?php

namespace App\Http\Controllers;


use App\Events\GameMessageCreated;
use App\GameMessage;
use Auth;
use Illuminate\Http\Request;
use Validator;

class GameMessageController extends Controller
{
    public function index($gameId)
    {

        $messages = GameMessage::whereGameId($gameId)->with('user')->get();
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

        $mes = [];
        $mes['message'] = $message->message;
        $mes['game_id'] = $message->game_id;
        $mes['date'] = $message['date'];
        $mes['time'] = $message['time'];
        $mes['user'] = ['name' => $user->name];

        broadcast(new GameMessageCreated($mes));
        return $message;
    }
}