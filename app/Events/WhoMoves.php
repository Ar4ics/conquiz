<?php

namespace App\Events;

use App\UserColor;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class WhoMoves implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_color;
    public $game_id;

    public function __construct($userColor, $game_id)
    {
        $this->user_color = $userColor;
        $this->game_id = $game_id;
    }

    public function broadcastOn()
    {
        return new Channel('game.' . $this->game_id);
    }

    public function broadcastWith()
    {
        return [
            'user_color' => $this->user_color,
        ];
    }
}
