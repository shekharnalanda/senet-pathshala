<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningProgram extends Model
{
    protected $fillable = ['slug','name','card_text','intro','focus','learning_approach','image','sort_order','is_active'];
    protected $casts = ['is_active'=>'boolean'];
}
