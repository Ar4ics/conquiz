<?php

namespace App\Events;

use App\Question;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewQuestion implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $question;
    public $game_id;

    public function __construct(Question $question, $game_id)
    {
        $this->question = $question;
        $this->game_id = $game_id;
    }


    public function broadcastOn()
    {
        return new Channel('game.' . $this->game_id);
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->question->id,
            'title' => $this->question->title,
            'answers' => $this->question->answers,
            'is_exact_answer' => false
        ];
    }
}
