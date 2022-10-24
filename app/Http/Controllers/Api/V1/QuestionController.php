<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuestionController extends Controller
{

    public function store($quiz, Request $request)
    {
        $quiz = Quiz::find($quiz);

        if (!$quiz) return customResponse('400', 'No such quiz found', $quiz, false);

        if ($quiz->is_published) return customResponse('400', "You can't add questions to a published quiz", $quiz, false);


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

    public function update($quiz, $question, Request $request)
    {
        $quiz = Quiz::find($quiz);

        $question = Question::where('id', $question)->where('quiz_id', $quiz->id)->first();

        if (!$quiz) return customResponse('400', 'No such quiz found', $quiz, false);

        if ($quiz->is_published) return customResponse('400', "You can't update questions of a published quiz", $quiz, false);

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

    public function delete($quiz_id, $question_id, Request $request)
    {
        $quiz = Quiz::find($quiz_id);

        if (!$quiz) return customResponse('400', 'No such quiz found', $quiz, false);

        if ($quiz->is_published) return customResponse('400', "You can't delete questions of a published quiz", $quiz, false);

        $question = Question::where('id', $question_id)->where('quiz_id', $quiz_id)->first();

        if (!$question) {
            return customResponse('400', 'No such question found in this quiz', $question, false);
        }

        $question->answers()->delete();

        $question->delete();

        return customResponse('200', 'Question deleted successfuly', $question, true);
    }
}
