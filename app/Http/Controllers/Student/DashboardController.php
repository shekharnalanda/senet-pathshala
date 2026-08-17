<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Homework;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $student = $request->user()->student;
        abort_unless($student && $student->is_active, 403);
        return view('student.dashboard', compact('student'));
    }

    public function profile(Request $request): View
    {
        $student = $request->user()->student;
        abort_unless($student && $student->is_active, 403);
        $student->load('user');
        return view('student.profile', compact('student'));
    }

    public function idCard(Request $request): View
    {
        $student = $request->user()->student;
        abort_unless($student && $student->is_active, 403);
        $student->load('user');
        return view('student.id-card', compact('student'));
    }

    public function attendance(Request $request): View
    {
        $student = $request->user()->student;
        abort_unless($student && $student->is_active, 403);
        $records = Attendance::where('student_id', $student->id)->latest('attendance_date')->get();
        $present = $records->where('status', 'Present')->count();
        $absent = $records->where('status', 'Absent')->count();
        $leave = $records->where('status', 'Leave')->count();
        $workingDays = $present + $absent;
        $percentage = $workingDays > 0 ? round(($present / $workingDays) * 100, 1) : 0;
        return view('student.attendance', compact('student', 'records', 'present', 'absent', 'leave', 'percentage'));
    }

    public function homework(Request $request): View
    {
        $student = $request->user()->student;
        abort_unless($student && $student->is_active, 403);
        $homeworks = Homework::where('class_name', $student->class_name)
            ->where(function ($query) use ($student) {
                $query->whereNull('section')->orWhere('section', '')->orWhere('section', $student->section);
            })->latest('homework_date')->latest('id')->get();
        return view('student.homework', compact('student', 'homeworks'));
    }
}
