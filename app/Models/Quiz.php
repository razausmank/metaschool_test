<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $guarded =  [];

    // local scope
    public function scopePublished($query)
    {
        return $query->where('is_published', 1);
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'quiz_id');
    }
}
