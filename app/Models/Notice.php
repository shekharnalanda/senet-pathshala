<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $fillable = [
        'title',
        'description',
        'notice_date',
        'status',
    ];

    protected $casts = [
        'notice_date' => 'date',
        'status' => 'boolean',
    ];
}
