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

class WhoAttack implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $competitive_box;
    public $game_id;

    public function __construct($competitive_box, $game_id)
    {
        $this->competitive_box = $competitive_box;
        $this->game_id = $game_id;
    }

    public function broadcastOn()
    {
        return new Channel('game.' . $this->game_id);
    }

    public function broadcastWith()
    {
        return [
            'competitive_box' => $this->competitive_box,
        ];
    }
}
