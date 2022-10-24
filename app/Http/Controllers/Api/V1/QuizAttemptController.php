<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswers;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizAttemptController extends Controller
{
    public function index()
    {
        $quizAttempts = QuizAttempt::where('user_id', auth()->user()->id)->get();

        return customResponse(200, 'Quiz taken succesfully retrieved', $quizAttempts, true, count($quizAttempts));
    }

    public function submit($id, Request $request)
    {
        $quiz = Quiz::find($id);

        if (!$quiz) return customResponse('400', 'No such quiz found', $quiz, false);

        if (!$quiz->is_published) return customResponse('400', "You can't attempt an  unpublished quiz", $quiz, false);

        $quizAttempt = null;
        try {


            DB::transaction(function () use ($request, &$quiz, &$quizAttempt) {

                $quizAttempt = QuizAttempt::create([
                    'quiz_id' => $quiz->id,
                    'user_id' => auth()->user()->id,
                    'no_of_questions' => $quiz->questions->count(),
                ]);

                foreach ($request->answers as $answer) {

                    $question = Question::where('id', $answer["question_id"])->where('quiz_id', $quiz->id)->first();

                    QuizAttemptAnswers::create([
                        'quiz_attempt_id' => $quizAttempt->id,
                        'question_id' => $question->id,
                        'answer' => $answer["answer"],
                        'is_correct' => $question->check_answer($answer["answer"]),
                    ]);
                }

                $quizAttempt->update([
                    'correct_answers' => $quizAttempt->number_of_correct_answers()
                ]);
            });
        } catch (Exception $e) {
            return customResponse(400, 'Malformed Request', $e, false);
        }


        return customResponse(200, 'Quiz succesfully submitted', $quizAttempt, true);
    }
}
