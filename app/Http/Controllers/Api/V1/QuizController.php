<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\QuizPublished;
use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{

    public function __construct(Request $request)
    {
        // makes sure the quiz is present, its not published and it is belongs to the current user
        // defined here so i dont have to define in each function
        $this->middleware(function ($request, $next) {
            $quiz = Quiz::find(request()->route('id'));

            if (!$quiz) return customResponse('400', 'No such quiz found', $quiz, false);

            if ($quiz->created_by != auth()->user()->id) return customResponse('400', "You can't modify someone else's quiz", $quiz, false);

            return $next($request);
        })->except('store');
    }

    /**
     * store
     *
     * @param  Request $request
     * @return Json
     */
    public function store(Request $request)
    {
        $request->validate([
            'topic' => 'required|string'
        ]);

        $quiz = Quiz::create([
            'topic' => $request->topic
        ]);

        return customResponse('201', 'Quiz created successfuly', $quiz, true);
    }

    /**
     * update
     *
     * @param  mixed $id
     * @param  Request $request
     * @return Json
     */
    public function update($id, Request $request)
    {
        $quiz = Quiz::find($id);

        $request->validate([
            'topic' => 'required|string'
        ]);

        $quiz->update([
            'topic' => $request->topic
        ]);

        return customResponse('200', 'Quiz updated successfuly', $quiz, true);
    }


    /**
     * delete
     *
     * @param  mixed $id
     * @param  Request $request
     * @return Json
     */
    public function delete($id, Request $request)
    {
        $quiz = Quiz::find($id);

        $quiz->delete();

        return customResponse('200', 'Quiz deleted successfuly', $quiz, true);
    }

    /**
     * Publishes unpublished quiz and dispatches event to send emails
     *
     * @param  mixed $id
     * @param  Request $request
     * @return Json
     */
    public function publishQuiz($id, Request $request)
    {
        $quiz = Quiz::find($id);
        if ($quiz->is_published) return customResponse('400', 'Quiz is already published', $quiz, false);

        $quiz->update([
            'is_published' => 1
        ]);

        // dispatch the quiz published event so emails can be sent in background
        QuizPublished::dispatch($quiz->created_by, $quiz->id);

        return customResponse('200', 'Quiz published successfully', $quiz, true);
    }
}
