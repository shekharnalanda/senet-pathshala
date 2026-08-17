<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        $students = Student::with('user')->where('is_active', true)->orderBy('class_name')->orderBy('roll_no')->get();
        $attendance = Attendance::whereDate('attendance_date', $date)->get()->keyBy('student_id');

        return view('admin.attendance.index', compact('date', 'students', 'attendance'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'attendance_date' => ['required', 'date'],
            'attendance' => ['required', 'array'],
            'attendance.*.status' => ['required', 'in:Present,Absent,Leave'],
            'attendance.*.remark' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($data['attendance'] as $studentId => $row) {
            if (! Student::whereKey($studentId)->where('is_active', true)->exists()) continue;
            Attendance::updateOrCreate(
                ['student_id' => $studentId, 'attendance_date' => $data['attendance_date']],
                ['status' => $row['status'], 'remark' => $row['remark'] ?? null]
            );
        }

        return redirect()->route('admin.attendance.index', ['date' => $data['attendance_date']])
            ->with('success', 'Attendance saved successfully.');
    }
}
