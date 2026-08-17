<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('homeworks', function (Blueprint $table) {
            $table->id();
            $table->string('class_name', 100);
            $table->string('section', 50)->nullable();
            $table->string('subject', 150);
            $table->date('homework_date');
            $table->date('due_date')->nullable();
            $table->text('details');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homeworks');
    }
};
