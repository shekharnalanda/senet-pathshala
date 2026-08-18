<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->string('about_hero_image')->nullable()->after('about_content');
            $table->string('about_image_one')->nullable()->after('about_hero_image');
            $table->string('about_image_two')->nullable()->after('about_image_one');
        });
    }

    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn(['about_hero_image','about_image_one','about_image_two']);
        });
    }
};
