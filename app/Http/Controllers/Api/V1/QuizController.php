<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{

    public function all_quizzes()
    {
        $quizzes = Quiz::published()->get();

        return customResponse('200', 'All Quiz retrieved successfuly', $quizzes, true, count($quizzes));
    }

    public function current_user_quizzes($filter, Request $request)
    {
        $request->validate([
            'filter' => 'required|in:all,published,unpublished'
        ]);

        // filter validation can be only published, unpublished, all

        $quizzes = Quiz::where()->get();

        return customResponse('200', 'All Quiz retrieved successfuly', $quizzes, true, count($quizzes));
    }

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

    public function update($id, Request $request)
    {
        $quiz = Quiz::find($id);

        if (!$quiz) return customResponse('400', 'No such quiz found', $quiz, false);

        $request->validate([
            'topic' => 'required|string'
        ]);

        $quiz->update([
            'topic' => $request->topic
        ]);

        return customResponse('200', 'Quiz updated successfuly', $quiz, true);
    }

    public function delete($id, Request $request)
    {
        $quiz = Quiz::find($id);

        if (!$quiz) return customResponse('400', 'No such quiz found', $quiz, false);

        $quiz->delete();

        return customResponse('200', 'Quiz deleted successfuly', $quiz, true);
    }

    public function publish_quiz($quiz_id, Request $request)
    {
        $quiz = Quiz::find($quiz_id);

        if (!$quiz) return customResponse('400', 'No such quiz found', $quiz, false);

        if ($quiz->is_published) return customResponse('400', 'Quiz is already published', $quiz, false);

        $quiz->update([
            'is_published' => 1
        ]);

        return customResponse('200', 'Quiz published successfully', $quiz, true);
    }

    public function show($id)
    {
        $quiz = Quiz::with('questions.options_without_correct_answers')->where('id', $id)->first();

        if (!$quiz) return customResponse('400', 'No such quiz found', $quiz, false);

        return customResponse('200', 'Quiz retrieved successfully', $quiz, true);
    }
}
