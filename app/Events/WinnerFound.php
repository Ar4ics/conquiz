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

class WinnerFound implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $winner;
    public $game_id;

    public function __construct(UserColor $winner, $game_id)
    {
        $this->winner = $winner;
        $this->game_id = $game_id;
    }

    public function broadcastOn()
    {
        return new Channel('game.' . $this->game_id);
    }

    public function broadcastWith()
    {
        return [
            'winner' => $this->winner
        ];
    }
}
