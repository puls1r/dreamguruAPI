<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    public function questions(){
        return $this->belongsToMany(Question::class, 'quiz_questions')->withPivot('order');
    }
}
