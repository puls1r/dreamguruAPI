<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    public function quizzes(){
        return $this->belongsToMany(Quiz::class, 'quiz_questions')->withPivot('order');
    }

    public function answers(){
        return $this->hasMany(Answer::class);
    }
}
