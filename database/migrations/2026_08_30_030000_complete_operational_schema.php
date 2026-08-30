<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('school_classes')) {
            Schema::create('school_classes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('branch_id')->nullable()->index();
                $table->string('name');
                $table->string('sections')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        } else {
            Schema::table('school_classes', function (Blueprint $table) {
                if (! Schema::hasColumn('school_classes', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable()->index();
                }
                if (! Schema::hasColumn('school_classes', 'name')) {
                    $table->string('name')->nullable();
                }
                if (! Schema::hasColumn('school_classes', 'sections')) {
                    $table->string('sections')->nullable();
                }
                if (! Schema::hasColumn('school_classes', 'sort_order')) {
                    $table->unsignedInteger('sort_order')->default(0);
                }
                if (! Schema::hasColumn('school_classes', 'is_active')) {
                    $table->boolean('is_active')->default(true);
                }
            });
        }

        if (! Schema::hasTable('student_documents')) {
            Schema::create('student_documents', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id')->index();
                $table->string('title');
                $table->string('document_type')->nullable();
                $table->string('file_path');
                $table->text('note')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('student_documents', function (Blueprint $table) {
                if (! Schema::hasColumn('student_documents', 'student_id')) {
                    $table->unsignedBigInteger('student_id')->nullable()->index();
                }
                if (! Schema::hasColumn('student_documents', 'title')) {
                    $table->string('title')->nullable();
                }
                if (! Schema::hasColumn('student_documents', 'document_type')) {
                    $table->string('document_type')->nullable();
                }
                if (! Schema::hasColumn('student_documents', 'file_path')) {
                    $table->string('file_path')->nullable();
                }
                if (! Schema::hasColumn('student_documents', 'note')) {
                    $table->text('note')->nullable();
                }
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (! Schema::hasColumn('users', 'role')) {
                    $table->string('role')->nullable();
                }
                if (! Schema::hasColumn('users', 'assigned_class')) {
                    $table->string('assigned_class')->nullable();
                }
                if (! Schema::hasColumn('users', 'assigned_section')) {
                    $table->string('assigned_section')->nullable();
                }
                if (! Schema::hasColumn('users', 'permissions')) {
                    $table->json('permissions')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        // Forward-only compatibility repair. Existing production schema is intentionally preserved.
    }
};
