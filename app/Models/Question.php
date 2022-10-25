<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function answers()
    {
        return $this->hasMany(Answer::class, 'question_id');
    }

    public function correct_answer()
    {
        return $this->hasMany(Answer::class, 'question_id')->where('is_correct', 1);
    }

    public function check_answer($answer)
    {
        return $this->correct_answer()->first()->answer == $answer;
    }
}
