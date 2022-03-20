<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserQuizQuestion extends Model
{
    use HasFactory;

    public function question(){
        return $this->belongsTo(Question::class);
    }

    public function user_quiz_question_answers(){
        return $this->hasMany(UserQuizQuestionAnswer::class);
    }
}
