<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserQuizQuestionAnswer extends Model
{
    use HasFactory;

    public function user_quiz_question(){
        return $this->belongsTo(UserQuizQuestion::class);
    }
}
