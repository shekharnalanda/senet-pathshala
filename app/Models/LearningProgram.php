<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningProgram extends Model
{
    protected $fillable = ['branch_id','slug','name','card_text','intro','focus','learning_approach','image','sort_order','is_active'];
    protected $casts = ['is_active'=>'boolean'];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
