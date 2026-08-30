<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('learning_programs')) {
            Schema::create('learning_programs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
                $table->string('slug')->unique();
                $table->string('name');
                $table->text('card_text')->nullable();
                $table->text('intro')->nullable();
                $table->text('focus')->nullable();
                $table->text('learning_approach')->nullable();
                $table->string('image')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Intentionally non-destructive: this migration repairs an operational schema.
    }
};
