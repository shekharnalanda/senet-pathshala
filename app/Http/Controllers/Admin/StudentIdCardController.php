<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentIdCardController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->is_admin, 403);

        $search = trim((string) $request->query('q', ''));
        $branchId = $request->filled('branch_id') ? $request->integer('branch_id') : null;
        $className = trim((string) $request->query('class_name', ''));
        $section = trim((string) $request->query('section', ''));

        if ($branchId && ! Branch::whereKey($branchId)->where('is_active', true)->exists()) {
            $branchId = null;
            $className = '';
            $section = '';
        }

        $classes = SchoolClass::where('is_active', true)
            ->when($branchId, fn ($q) => $q->where(function ($x) use ($branchId) {
                $x->whereNull('branch_id')->orWhere('branch_id', $branchId);
            }))
            ->orderBy('sort_order')->orderBy('name')->get();

        $sections = collect();
        if ($className !== '') {
            $matchingClasses = $classes->where('name', $className);
            if ($matchingClasses->isEmpty()) {
                $className = '';
                $section = '';
            } else {
                $sections = $matchingClasses
                    ->flatMap(fn ($class) => $class->section_list ?? [])
                    ->filter()->unique()->sort()->values();

                if ($section !== '' && ! $sections->contains($section)) {
                    $section = '';
                }
            }
        } elseif ($section !== '') {
            $section = '';
        }

        $query = Student::with(['user', 'branch'])->where('is_active', true)
            ->whereNotNull('branch_id')
            ->whereHas('branch', fn ($q) => $q->where('is_active', true))
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->when($className !== '', fn ($q) => $q->where('class_name', $className))
            ->when($section !== '', fn ($q) => $q->where('section', $section));

        if ($search !== '') {
            $query->where(function ($studentQuery) use ($search) {
                $studentQuery->where('admission_no', 'like', '%'.$search.'%')
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', '%'.$search.'%'));
            });
        }

        $students = $query->orderBy('class_name')->orderBy('section')->orderBy('roll_no')->get();
        $branches = Branch::where('is_active', true)->orderByDesc('is_main')->orderBy('name')->get();

        return view('admin.student-id-cards.index', compact(
            'students', 'branches', 'classes', 'sections', 'search', 'branchId', 'className', 'section'
        ));
    }

    public function print(Request $request): View
    {
        abort_unless($request->user()->is_admin, 403);

        $data = $request->validate([
            'students' => ['required', 'array', 'min:1', 'max:100'],
            'students.*' => ['required', 'integer', 'distinct', 'exists:students,id'],
        ]);

        $selectedIds = array_values($data['students']);
        $found = Student::with(['user', 'branch'])
            ->where('is_active', true)
            ->whereNotNull('branch_id')
            ->whereHas('branch', fn ($q) => $q->where('is_active', true))
            ->whereIn('id', $selectedIds)
            ->get()
            ->keyBy('id');

        abort_unless($found->count() === count($selectedIds), 422, 'One or more selected students are inactive or are not assigned to an active campus.');

        $students = collect($selectedIds)->map(fn ($id) => $found->get($id));
        $settings = SchoolSetting::first();

        return view('admin.student-id-cards.print', compact('students', 'settings'));
    }
}
