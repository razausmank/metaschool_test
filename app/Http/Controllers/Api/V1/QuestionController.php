<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function __construct(Request $request)
    {
        // makes sure the quiz is present, its not published and it belongs to the current user
        // defined here so i dont have to define in each function
        $this->middleware(function ($request, $next) {
            $quiz = Quiz::find(request()->route('id'));

            if (!$quiz) return customResponse('400', 'No such quiz found', $quiz, false);

            if ($quiz->is_published) return customResponse('400', "You can't modify a published quiz", $quiz, false);

            if ($quiz->created_by != auth()->user()->id) return customResponse('400', "You can't modify someone else's quiz", $quiz, false);

            return $next($request);
        });
    }

    /**
     * Add question to your unpublished quiz
     *
     * @param  mixed $quiz
     * @param  Request $request
     * @return Json
     */
    public function store($quiz, Request $request)
    {
        $quiz = Quiz::find($quiz);

        $request->validate([
            'question' => 'required|string',
            'is_mcq' => 'required|boolean'
        ]);

        $question = Question::create([
            'question' => $request->question,
            'is_mcq' => $request->is_mcq,
            'quiz_id' => $quiz->id
        ]);


        if ($request->answers) {
            foreach ($request->answers as $answer) {

                Answer::create([
                    'answer' => $answer["answer"],
                    'is_correct' => $request->is_mcq == 0 ? 1 : ($answer["is_correct"] ?? 0),
                    'question_id' => $question->id
                ]);
            }
        }

        return customResponse('200', 'Question added to quiz successfuly', $question, true);
    }


    /**
     * update question of your unpublished quiz
     *
     * @param  mixed $quiz
     * @param  mixed $question
     * @param  Request $request
     * @return Json
     */
    public function update($quiz, $question, Request $request)
    {
        $quiz = Quiz::find($quiz);

        $question = Question::where('id', $question)->where('quiz_id', $quiz->id)->first();

        if (!$question) return customResponse('400', 'No such question found', $question, false);

        $request->validate([
            'question' => 'required|string',
            'is_mcq' => 'required|boolean'
        ]);

        $question->update([
            'question' => $request->question,
            'is_mcq' => $request->is_mcq,
        ]);

        if ($request->answers) {
            // delete the previously created answer and create answers again
            // since its easier in this case
            $question->answers()->delete();

            foreach ($request->answers as $answer) {
                Answer::create([
                    'answer' => $answer["answer"],
                    'is_correct' => $request->is_mcq == 0 ? 1 : ($answer["is_correct"] ?? 0),
                    'question_id' => $question->id
                ]);
            }
        }

        return customResponse('200', 'Question added to quiz successfuly', $question, true);
    }


    /**
     * delete question from your unpublished quiz
     *
     * @param  mixed $quiz_id
     * @param  mixed $question_id
     * @param  Request $request
     * @return Json
     */
    public function delete($quiz_id, $question_id, Request $request)
    {
        $question = Question::where('id', $question_id)->where('quiz_id', $quiz_id)->first();

        if (!$question) {
            return customResponse('400', 'No such question found in this quiz', $question, false);
        }

        $question->answers()->delete();

        $question->delete();

        return customResponse('200', 'Question deleted successfuly', $question, true);
    }
}
