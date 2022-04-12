<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $with = ['students'];

    public function course_sections(){
        return $this->hasMany(CourseSection::class);
    }

    public function teacher(){
        return $this->belongsTo(User::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function students(){
        return $this->belongsToMany(User::class, 'user_courses')->withPivot('is_purchased', 'status', 'certificate')->withTimestamps();
    }

    public function ratings(){
        return $this->belongsToMany(User::class, 'user_ratings')->withPivot('rating', 'comment');
    }
}
