<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamResult extends Model
{
    use HasFactory;

    protected $table = 'exam_results';
    protected $fillable = ['student_id','exam_name','subject','max_marks','marks_obtained','exam_date','remark'];
    protected $casts = ['exam_date' => 'date', 'max_marks' => 'decimal:2', 'marks_obtained' => 'decimal:2'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
