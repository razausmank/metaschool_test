<?php

namespace App\Listeners;

use App\Events\QuizPublished;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendNewQuizNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\QuizPublished  $event
     * @return void
     */
    public function handle(QuizPublished $event)
    {
        $quiz_owner = User::find($event->userId);
        $users = User::where('id', '!=', $event->userId)->get();
        $quiz = Quiz::find($event->quizId);

        $email_content = "A new Quiz has been created by " . $quiz_owner->name . " on the topic  '" . $quiz->topic . "', go check it out";
        // send mail to all users
        foreach ($users as $user) {

            Mail::raw($email_content, function ($m) use ($user) {
                $m->to($user->email)->subject('New Quiz Published');
            });
        }
    }
}
