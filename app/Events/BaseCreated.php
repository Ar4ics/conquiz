<?php

namespace App\Events;

use App\Box;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BaseCreated implements ShouldBroadcast
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
            'base_guards_count' => $this->box->user_color->base_guards_count,
            'cost' => $this->box->cost,
            'user_color_id' => $this->box->user_color_id,
        ];
    }
}