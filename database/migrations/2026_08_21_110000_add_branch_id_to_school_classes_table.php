<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('school_classes') || Schema::hasColumn('school_classes', 'branch_id')) {
            return;
        }

        Schema::table('school_classes', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('id')->constrained('branches')->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('school_classes') || ! Schema::hasColumn('school_classes', 'branch_id')) {
            return;
        }

        Schema::table('school_classes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('branch_id');
        });
    }
};
