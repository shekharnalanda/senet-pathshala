<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentIdCardController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $query = Student::with('user')->where('is_active', true);

        if ($request->user()->isClassTeacher()) {
            $query->where('class_name', $request->user()->assigned_class);
            if ($request->user()->assigned_section) {
                $query->where('section', $request->user()->assigned_section);
            }
        }

        if ($search !== '') {
            $query->where(function ($studentQuery) use ($search) {
                $studentQuery->where('admission_no', 'like', '%'.$search.'%')
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', '%'.$search.'%'));
            });
        }

        $students = $query
            ->orderBy('class_name')
            ->orderBy('roll_no')
            ->get();

        return view('admin.student-id-cards.index', compact('students', 'search'));
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
