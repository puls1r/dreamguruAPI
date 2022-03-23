<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseSection extends Model
{
    use HasFactory;

    protected $with = ['quizzes', 'parts', 'assignments'];

    public function course(){
        return $this->belongsTo(Course::class);
    }

    public function quizzes(){
        return $this->hasMany(SectionQuiz::class);
    }

    public function parts(){
        return $this->hasMany(SectionPart::class);
    }

    public function assignments(){
        return $this->hasMany(Assignment::class);
    }

    public function discussions(){
        return $this->hasMany(SectionDiscussion::class);
    }
}
