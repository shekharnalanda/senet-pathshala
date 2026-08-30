<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('school_settings')) {
            return;
        }

        foreach (['about_hero_image', 'about_image_one', 'about_image_two'] as $column) {
            if (! Schema::hasColumn('school_settings', $column)) {
                Schema::table('school_settings', function (Blueprint $table) use ($column) {
                    $table->string($column)->nullable();
                });
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('school_settings')) {
            return;
        }

        $columns = array_values(array_filter(
            ['about_hero_image', 'about_image_one', 'about_image_two'],
            fn (string $column) => Schema::hasColumn('school_settings', $column)
        ));

        if ($columns !== []) {
            Schema::table('school_settings', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
