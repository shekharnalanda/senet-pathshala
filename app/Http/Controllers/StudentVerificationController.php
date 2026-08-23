<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use App\Models\Student;
use Illuminate\View\View;

class StudentVerificationController extends Controller
{
    public function show(string $admissionNo): View
    {
        $student = Student::with(['user','branch'])
            ->where('admission_no', $admissionNo)
            ->where('is_active', true)
            ->whereNotNull('branch_id')
            ->whereHas('branch', fn ($branch) => $branch->where('is_active', true))
            ->whereHas('user', fn ($user) => $user->whereColumn('users.branch_id', 'students.branch_id'))
            ->firstOrFail();

        $settings = SchoolSetting::first();

        return view('student.verify', compact('student', 'settings'));
    }
}
