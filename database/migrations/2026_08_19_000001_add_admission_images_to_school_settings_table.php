<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->string('admission_hero_image')->nullable();
            $table->string('admission_image_one')->nullable();
            $table->string('admission_image_two')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn(['admission_hero_image','admission_image_one','admission_image_two']);
        });
    }
};
