<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CorrectAnswers implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $results;
    public $competitive_box;
    public $game_id;
    public $correct;
    public $winner;


    public function __construct($results, $correct, $game_id)
    {
        $this->results = $results;
        $this->correct = $correct;
        $this->game_id = $game_id;
    }


    public function broadcastOn()
    {
        return new Channel('game.' . $this->game_id);
    }

    public function broadcastWith()
    {
        return [
            'results' => $this->results,
            'correct' => $this->correct
        ];
    }
}