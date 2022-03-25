<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionPart extends Model
{
    use HasFactory;

    public function section(){
        return $this->belongsTo(CourseSection::class);
    }

    protected static function boot()
    {
        parent::boot();
        SectionPart::created(function ($model) {
            $model->slug = 'part' . $model->id;
            $model->save();
        });
    }
}
