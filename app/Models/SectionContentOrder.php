<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionContentOrder extends Model
{
    use HasFactory;

    public function course_section(){
        return $this->belongsTo(CourseSection::class);
    }
}
