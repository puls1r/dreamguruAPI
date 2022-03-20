<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionDiscussion extends Model
{
    use HasFactory;

    public function course_section(){
        return $this->belongsTo(CourseSection::class);
    }

    public function discussion_replies(){
        return $this->hasMany(DiscussionReply::class);
    }
}
