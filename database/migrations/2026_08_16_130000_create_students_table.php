<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('admission_no')->unique();
            $table->string('class_name')->nullable();
            $table->string('section', 20)->nullable();
            $table->string('roll_no', 50)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
