<?php

namespace App\Http\Controllers;


use App\Events\GameMessageCreated;
use App\GameMessage;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Log;
use Validator;

class GameMessageController extends Controller
{
    public function index($gameId) {

        $messages = GameMessage::whereGameId($gameId)->with('user')->get();
        $messages->each(function (GameMessage $message) {
            $message['date'] = $message->created_at->diffForHumans();
        });
        return $messages;
    }

    public function store(Request $request, $gameId) {

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
        $mes['date'] = $message->created_at->diffForHumans();
        $mes['user'] = ['name' => $user->name];

        event(new GameMessageCreated($mes));
        return $message;
    }
}