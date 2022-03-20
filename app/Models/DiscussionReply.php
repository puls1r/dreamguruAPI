<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscussionReply extends Model
{
    use HasFactory;

    public function section_discussion(){
        return $this->belongsTo(SectionDiscussion::class);
    }
}
