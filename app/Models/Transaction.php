<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();
        Transaction::created(function ($model) {
            $model->order_id = 'DX-'.Str::random(5);
            $model->save();
        });
    }
}
