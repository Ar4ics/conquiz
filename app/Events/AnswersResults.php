<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class AnswersResults implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $results;
    public $boxes;
    public $game_id;


    public function __construct($results, $boxes, $game_id)
    {
        $this->results = $results;
        $this->boxes = $boxes;
        $this->game_id = $game_id;
    }


    public function broadcastOn()
    {
        return new PrivateChannel('game.' . $this->game_id);
    }

    public function broadcastWith()
    {
        return [
            'results' => $this->results,
            'boxes' => $this->boxes
        ];
    }
}
