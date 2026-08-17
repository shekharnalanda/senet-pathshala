<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentIdCardController extends Controller
{
    public function index(): View
    {
        $students = Student::with('user')
            ->where('is_active', true)
            ->orderBy('class_name')
            ->orderBy('roll_no')
            ->get();

        return view('admin.student-id-cards.index', compact('students'));
    }

    public function print(Request $request): View
    {
        $data = $request->validate([
            'students' => ['required', 'array', 'size:2'],
            'students.*' => ['required', 'integer', 'distinct', 'exists:students,id'],
        ]);

        $selectedIds = $data['students'];
        $found = Student::with('user')
            ->where('is_active', true)
            ->whereIn('id', $selectedIds)
            ->get()
            ->keyBy('id');

        abort_unless($found->count() === 2, 422, 'Please select two active students.');

        $students = collect($selectedIds)->map(fn ($id) => $found->get($id));

        return view('admin.student-id-cards.print', compact('students'));
    }
}
