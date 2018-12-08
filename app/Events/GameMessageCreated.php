<?php

namespace App\Events;

use App\GameMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameMessageCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $gameMessage;

    public function __construct($gameMessage)
    {
        $this->gameMessage = $gameMessage;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('game.' . $this->gameMessage['game_id']);
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->gameMessage['message'],
            'user' => $this->gameMessage['user'],
            'date' => $this->gameMessage['date'],
            'time' => $this->gameMessage['time'],
        ];
    }
}