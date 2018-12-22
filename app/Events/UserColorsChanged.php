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

class UserColorsChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userColors;
    public $game_id;

    public function __construct($game_id)
    {
        $this->userColors = UserColor::with('user')
            ->whereGameId($game_id)->orderBy('score', 'desc')->get()->toArray();
        $this->game_id = $game_id;

    }

    public function broadcastOn()
    {
        return new PrivateChannel('game.' . $this->game_id);
    }

    public function broadcastWith()
    {
        return [
            'user_colors' => $this->userColors,
        ];
    }
}
