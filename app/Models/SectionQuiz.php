<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionQuiz extends Model
{
    use HasFactory;

    public function course_section(){
        return $this->belongsTo(CourseSection::class);
    }

    public function user_quizzes(){
        return $this->hasMany(UserQuiz::class);
    }

    public function quiz(){
        return $this->belongsTo(Quiz::class);
    }

    protected static function boot()
    {
        parent::boot();
        SectionQuiz::created(function ($model) {
            $model->slug = 'quiz' . $model->id;
            $model->save();
        });
    }
}
