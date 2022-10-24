<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function attempt_answers()
    {
        return $this->hasMany(QuizAttemptAnswers::class, 'quiz_attempt_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function number_of_correct_answers()
    {
        return $this->attempt_answers->where('is_correct', 1)->count();
    }
}
