<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamResult;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResultController extends Controller
{
    public function index(): View
    {
        $students = Student::with('user')->where('is_active', true)->orderBy('admission_no')->get();
        $results = ExamResult::with('student.user')->latest('exam_date')->latest('id')->get();
        return view('admin.results.index', compact('students', 'results'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'exam_name' => ['required', 'string', 'max:150'],
            'subject' => ['required', 'string', 'max:150'],
            'max_marks' => ['required', 'numeric', 'min:0.01'],
            'marks_obtained' => ['required', 'numeric', 'min:0'],
            'exam_date' => ['nullable', 'date'],
            'remark' => ['nullable', 'string', 'max:255'],
        ]);

        if ((float) $data['marks_obtained'] > (float) $data['max_marks']) {
            return back()
                ->withErrors(['marks_obtained' => 'Marks obtained cannot be greater than maximum marks.'])
                ->withInput();
        }

        ExamResult::updateOrCreate(
            [
                'student_id' => $data['student_id'],
                'exam_name' => $data['exam_name'],
                'subject' => $data['subject'],
            ],
            [
                'max_marks' => $data['max_marks'],
                'marks_obtained' => $data['marks_obtained'],
                'exam_date' => $data['exam_date'] ?? null,
                'remark' => $data['remark'] ?? null,
            ]
        );

        return back()->with('success', 'Result saved successfully.');
    }

    public function destroy(ExamResult $result): RedirectResponse
    {
        $result->delete();
        return back()->with('success', 'Result deleted successfully.');
    }
}
