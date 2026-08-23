<?php

use App\Models\Branch;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Artisan::command('school:about', function () {
    $this->info('Senet Pathshala management system');
})->purpose('Show application information');

Artisan::command('school:health', function () {
    $failures = [];

    try {
        DB::select('select 1');
        $this->info('OK database connection');
    } catch (Throwable $e) {
        $failures[] = 'Database connection failed: '.$e->getMessage();
    }

    $requiredRoutes = [
        'home', 'admission', 'gallery', 'contact', 'student.login',
        'admin.login', 'admin.dashboard', 'admin.students.index',
        'admin.attendance.index', 'admin.homework.index', 'admin.results.index',
        'admin.fees.index', 'admin.student-id-cards.index', 'admin.documents.index',
        'admin.admission-applications.index',
    ];

    foreach ($requiredRoutes as $name) {
        if (! Route::has($name)) $failures[] = 'Missing route: '.$name;
    }
    $this->info('OK critical routes');

    if (! is_writable(storage_path())) $failures[] = 'storage directory is not writable';
    if (! is_writable(base_path('bootstrap/cache'))) $failures[] = 'bootstrap/cache is not writable';
    $this->info('OK writable runtime directories');

    $activeBranches = Branch::where('is_active', true)->count();
    $activeClasses = SchoolClass::where('is_active', true)->count();
    $activeStudents = Student::where('is_active', true)
        ->whereNotNull('branch_id')
        ->whereHas('branch', fn ($q) => $q->where('is_active', true))
        ->count();

    if ($activeBranches < 1) $failures[] = 'No active campus found';
    if ($activeClasses < 1) $failures[] = 'No active class found';

    $orphanStudents = Student::where('is_active', true)
        ->where(function ($q) {
            $q->whereNull('branch_id')
                ->orWhereDoesntHave('branch', fn ($branch) => $branch->where('is_active', true));
        })->count();
    if ($orphanStudents > 0) $failures[] = $orphanStudents.' active student(s) are not assigned to an active campus';

    $campusMismatch = Student::where('is_active', true)
        ->whereHas('user')
        ->get(['id', 'user_id', 'branch_id'])
        ->filter(fn ($student) => (int) $student->user?->branch_id !== (int) $student->branch_id)
        ->count();
    if ($campusMismatch > 0) $failures[] = $campusMismatch.' student login account(s) have a campus mismatch';

    $badStaff = User::whereIn('role', ['class_teacher', 'office_manager', 'principal'])
        ->where(function ($q) {
            $q->whereNull('branch_id')
                ->orWhereDoesntHave('branch', fn ($branch) => $branch->where('is_active', true));
        })->count();
    if ($badStaff > 0) $failures[] = $badStaff.' staff account(s) are not assigned to an active campus';

    $this->line("Active campuses: {$activeBranches}");
    $this->line("Active classes: {$activeClasses}");
    $this->line("Active campus students: {$activeStudents}");

    if ($failures) {
        foreach ($failures as $failure) $this->error('FAIL '.$failure);
        return self::FAILURE;
    }

    $this->info('HEALTH CHECK PASSED');
    return self::SUCCESS;
})->purpose('Run production-safe integrity checks without PHPUnit');
