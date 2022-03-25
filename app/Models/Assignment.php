<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    public function user_assignments(){
        return $this->hasMany(UserAssignment::class);
    }

    public function course_section(){
        return $this->belongsTo(CourseSection::class);
    }

    protected static function boot()
    {
        parent::boot();
        Assignment::created(function ($model) {
            $model->slug = 'assignment' . $model->id;
            $model->save();
        });
    }
}
