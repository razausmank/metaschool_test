<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\QuestionController;
use App\Http\Controllers\Api\V1\QuizAttemptController;
use App\Http\Controllers\Api\V1\QuizController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// public routes

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);




// protected routes

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);


    //  Quiz
    Route::get('/quizzes', [QuizController::class, 'all_quizzes']);
    Route::post('/quiz', [QuizController::class, 'store']);
    Route::patch('/quiz/{id}', [QuizController::class, 'update']);
    Route::delete('/quiz/{id}', [QuizController::class, 'delete']);
    Route::post('/publish/quiz/{quiz_id}', [QuizController::class, 'publish_quiz']);
    Route::get('/quiz/{id}', [QuizController::class, 'show']);

    // Questions - add/remove/edit questions to/from quiz

    Route::post('/quiz/{id}/question', [QuestionController::class, 'store']);
    Route::post('/quiz/{quiz_id}/question/{question_id}', [QuestionController::class, 'update']);
    Route::delete('/quiz/{quiz_id}/delete/question/{question_id}', [QuestionController::class, 'delete']);


    // Quiz Attempt
    Route::post('/attempt/quiz/{id}', [QuizAttemptController::class, 'submit']);
    Route::get('/attempts', [QuizAttemptController::class, 'index']);
});
