<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Homework extends Model
{
    use HasFactory;

    protected $table = 'homeworks';

    protected $fillable = ['class_name', 'section', 'subject', 'homework_date', 'due_date', 'details'];

    protected $casts = [
        'homework_date' => 'date',
        'due_date' => 'date',
    ];
}
