<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseSection extends Model
{
    use HasFactory;

    public function course(){
        return $this->belongsTo(Course::class);
    }

    public function quizzes(){
        return $this->hasMany(SectionQuiz::class);
    }

    public function available_quizzes(){
        return $this->quizzes()->where('status', '!=', 'archived');
    }

    public function parts(){
        return $this->hasMany(SectionPart::class);
    }

    public function available_parts(){
        return $this->parts()->where('status', '!=', 'archived');
    }

    public function assignments(){
        return $this->hasMany(Assignment::class);
    }

    public function available_assignments(){
        return $this->assignments()->where('status', '!=', 'archived');
    }

    public function discussions(){
        return $this->hasMany(SectionDiscussion::class);
    }

    public function section_content_orders(){
        return $this->hasMany(SectionContentOrder::class);
    }
}
