<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('exam_name', 150);
            $table->string('subject', 150);
            $table->decimal('max_marks', 8, 2)->default(100);
            $table->decimal('marks_obtained', 8, 2);
            $table->date('exam_date')->nullable();
            $table->string('remark')->nullable();
            $table->timestamps();
            $table->unique(['student_id','exam_name','subject']);
        });
    }
    public function down(): void { Schema::dropIfExists('exam_results'); }
};
