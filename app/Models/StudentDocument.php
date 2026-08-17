<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentDocument extends Model
{
    use HasFactory;

    protected $table = 'student_documents';
    protected $fillable = ['student_id','title','document_type','file_path','note'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
