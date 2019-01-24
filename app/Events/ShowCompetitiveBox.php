<?php

namespace App\Events;

use App\Box;
use App\UserColor;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShowCompetitiveBox implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $x;
    public $y;
    public $id;

    public function __construct($x, $y, $id)
    {
        $this->x = $x;
        $this->y = $y;
        $this->id = $id;

    }

    public function broadcastOn()
    {
        return new Channel('game.' . $this->id);
    }

    public function broadcastWith()
    {
        return [
            'x' => $this->x,
            'y' => $this->y,
        ];
    }
}
