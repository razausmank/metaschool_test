<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuizPublished
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $quizId;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($userId, $quizId)
    {
        $this->userId = $userId;
        $this->quizId = $quizId;
    }
}
