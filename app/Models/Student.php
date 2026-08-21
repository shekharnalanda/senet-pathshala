<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','branch_id','admission_no','class_name','section','roll_no','date_of_birth','gender',
        'father_name','mother_name','guardian_name','guardian_phone','address','photo','is_active',
    ];

    protected $casts = ['date_of_birth'=>'date','is_active'=>'boolean'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
