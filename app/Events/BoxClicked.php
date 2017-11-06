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

class BoxClicked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $box;

    public function __construct(Box $box)
    {
        $this->box = $box;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('game.' . $this->box->game_id);
    }

    public function broadcastWith()
    {
        return [
            'x' => $this->box->x,
            'y' => $this->box->y,
            'color' => $this->box->user_color->color,
        ];
    }
}
