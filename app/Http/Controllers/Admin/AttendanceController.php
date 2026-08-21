<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Branch;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('attendance'), 403);

        $user = $request->user();
        $date = $request->input('date', now()->format('Y-m-d'));
        $search = trim((string) $request->query('q', ''));
        $selectedBranchId = $user->is_admin ? $request->integer('branch_id') : (int) $user->branch_id;
        if ($user->is_admin && $selectedBranchId && ! Branch::whereKey($selectedBranchId)->where('is_active', true)->exists()) {
            $selectedBranchId = 0;
        }
        $selectedClass = $user->isClassTeacher() ? $user->assigned_class : trim((string) $request->query('class_name', ''));
        $selectedSection = $user->isClassTeacher() && $user->assigned_section ? $user->assigned_section : trim((string) $request->query('section', ''));

        if ($selectedClass !== '') {
            $classQuery = SchoolClass::where('is_active', true)->where('name', $selectedClass);
            if ($selectedBranchId) {
                $classQuery->where(function ($q) use ($selectedBranchId) {
                    $q->whereNull('branch_id')->orWhere('branch_id', $selectedBranchId);
                });
            }
            $selectedSchoolClass = $classQuery->first();
            if (! $selectedSchoolClass) {
                $selectedClass = '';
                $selectedSection = '';
            } elseif ($selectedSection !== '' && ! in_array($selectedSection, $selectedSchoolClass->section_list ?? [], true)) {
                $selectedSection = '';
            }
        }

        $query = Student::with(['user', 'branch'])->where('is_active', true);

        if ($user->is_admin) {
            if ($selectedBranchId) $query->where('branch_id', $selectedBranchId);
        } else {
            abort_unless($user->branch_id && Branch::whereKey($user->branch_id)->where('is_active', true)->exists(), 403, 'No active campus is assigned to this staff account.');
            $query->where('branch_id', $user->branch_id);
        }

        if ($selectedClass !== '') $query->where('class_name', $selectedClass);
        if ($selectedSection !== '') $query->where('section', $selectedSection);

        if ($search !== '') {
            $query->where(function ($studentQuery) use ($search) {
                $studentQuery->where('admission_no', 'like', '%'.$search.'%')
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', '%'.$search.'%'));
            });
        }

        $students = $query->orderBy('class_name')->orderBy('section')->orderBy('roll_no')->get();
        $attendance = Attendance::whereDate('attendance_date', $date)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()->keyBy('student_id');

        $branches = $user->is_admin
            ? Branch::where('is_active', true)->orderByDesc('is_main')->orderBy('name')->get()
            : Branch::whereKey($user->branch_id)->where('is_active', true)->get();

        if ($user->isClassTeacher()) {
            $classes = SchoolClass::where('is_active', true)
                ->where('name', $user->assigned_class)
                ->when($user->branch_id, fn ($q) => $q->where(function ($x) use ($user) {
                    $x->whereNull('branch_id')->orWhere('branch_id', $user->branch_id);
                }))
                ->orderBy('sort_order')->get();
        } else {
            $classes = SchoolClass::where('is_active', true)
                ->when($selectedBranchId, fn ($q) => $q->where(function ($x) use ($selectedBranchId) {
                    $x->whereNull('branch_id')->orWhere('branch_id', $selectedBranchId);
                }))
                ->orderBy('sort_order')->orderBy('name')->get();
        }

        return view('admin.attendance.index', compact(
            'date', 'students', 'attendance', 'search', 'branches', 'classes',
            'selectedBranchId', 'selectedClass', 'selectedSection'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('attendance'), 403);

        $user = $request->user();
        $data = $request->validate([
            'attendance_date' => ['required', 'date'],
            'branch_id' => ['nullable', Rule::exists('branches', 'id')->where(fn ($q) => $q->where('is_active', true))],
            'class_name' => ['nullable', 'string', 'max:100'],
            'section' => ['nullable', 'string', 'max:50'],
            'attendance' => ['required', 'array'],
            'attendance.*.status' => ['required', 'in:Present,Absent,Leave'],
            'attendance.*.remark' => ['nullable', 'string', 'max:255'],
        ]);

        if (! $user->is_admin) {
            abort_unless($user->branch_id && Branch::whereKey($user->branch_id)->where('is_active', true)->exists(), 403, 'No active campus is assigned to this staff account.');
            $data['branch_id'] = $user->branch_id;
        }

        if (! empty($data['class_name'])) {
            $class = SchoolClass::where('is_active', true)->where('name', $data['class_name'])
                ->when(! empty($data['branch_id']), fn ($q) => $q->where(function ($x) use ($data) {
                    $x->whereNull('branch_id')->orWhere('branch_id', $data['branch_id']);
                }))->first();
            abort_unless($class, 422, 'Selected class is not available for this campus.');
            if (! empty($data['section'])) {
                abort_unless(in_array($data['section'], $class->section_list ?? [], true), 422, 'Selected section is not available for this class.');
            }
        } elseif (! empty($data['section'])) {
            abort(422, 'Please select a class before selecting a section.');
        }

        foreach ($data['attendance'] as $studentId => $row) {
            $studentQuery = Student::whereKey($studentId)->where('is_active', true);

            if (! empty($data['branch_id'])) $studentQuery->where('branch_id', $data['branch_id']);
            if (! empty($data['class_name'])) $studentQuery->where('class_name', $data['class_name']);
            if (! empty($data['section'])) $studentQuery->where('section', $data['section']);

            if ($user->isClassTeacher()) {
                $studentQuery->where('class_name', $user->assigned_class);
                if ($user->assigned_section) $studentQuery->where('section', $user->assigned_section);
            }

            if (! $studentQuery->exists()) continue;

            Attendance::updateOrCreate(
                ['student_id' => $studentId, 'attendance_date' => $data['attendance_date']],
                ['status' => $row['status'], 'remark' => $row['remark'] ?? null]
            );
        }

        return redirect()->route('admin.attendance.index', array_filter([
            'date' => $data['attendance_date'],
            'branch_id' => $user->is_admin ? ($data['branch_id'] ?? null) : null,
            'class_name' => $data['class_name'] ?? null,
            'section' => $data['section'] ?? null,
        ]))->with('success', 'Attendance saved successfully.');
    }
}
