<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSectionPart extends Model
{
    use HasFactory;

    public function part(){
        return $this->belongsTo(SectionPart::class);
    }
}
