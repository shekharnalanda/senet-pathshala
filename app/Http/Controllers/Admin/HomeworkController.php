<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Homework;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HomeworkController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('homework'), 403);
        $user = $request->user();
        $query = Homework::with('branch');

        if (! $user->is_admin && $user->branch_id) {
            $query->where('branch_id', $user->branch_id);
        } elseif ($request->filled('branch_id') && Branch::where('is_active', true)->whereKey($request->integer('branch_id'))->exists()) {
            $query->where('branch_id', $request->integer('branch_id'));
        }

        if ($user->isClassTeacher()) {
            $query->where('class_name', $user->assigned_class);
            if ($user->assigned_section) $query->where('section', $user->assigned_section);
            $classes = SchoolClass::where('is_active', true)
                ->where('name', $user->assigned_class)
                ->where(function ($q) use ($user) {
                    $q->whereNull('branch_id')->orWhere('branch_id', $user->branch_id);
                })->orderBy('sort_order')->orderBy('name')->get();
        } else {
            $classes = SchoolClass::where('is_active', true)
                ->when(! $user->is_admin && $user->branch_id, fn ($q) => $q->where(function ($x) use ($user) {
                    $x->whereNull('branch_id')->orWhere('branch_id', $user->branch_id);
                }))
                ->orderBy('sort_order')->orderBy('name')->get();
        }

        $branches = Branch::where('is_active', true)
            ->when(! $user->is_admin && $user->branch_id, fn ($q) => $q->whereKey($user->branch_id))
            ->orderByDesc('is_main')->orderBy('name')->get();
        $homeworks = $query->latest('homework_date')->latest('id')->get();

        return view('admin.homework.index', compact('homeworks', 'classes', 'branches'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('homework'), 403);
        $data = $request->validate([
            'branch_id' => ['required', Rule::exists('branches', 'id')->where('is_active', true)],
            'class_name' => ['required', 'string', 'max:100'],
            'section' => ['nullable', 'string', 'max:50'],
            'subject' => ['required', 'string', 'max:150'],
            'homework_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:homework_date'],
            'details' => ['required', 'string', 'max:5000'],
        ]);

        $user = $request->user();
        if (! $user->is_admin && $user->branch_id) {
            abort_unless((int) $data['branch_id'] === (int) $user->branch_id, 403);
        }
        if ($user->isClassTeacher()) {
            abort_unless($data['class_name'] === $user->assigned_class, 403);
            if ($user->assigned_section) {
                abort_unless(($data['section'] ?? null) === $user->assigned_section, 403);
            }
        }

        $class = SchoolClass::where('name', $data['class_name'])
            ->where('is_active', true)
            ->where(function ($q) use ($data) {
                $q->whereNull('branch_id')->orWhere('branch_id', $data['branch_id']);
            })->first();
        abort_unless($class, 422, 'Selected class is not available for this campus.');

        if (! empty($data['section']) && ! empty($class->section_list)) {
            abort_unless(in_array($data['section'], $class->section_list, true), 422, 'Selected section is not available for this class.');
        }

        Homework::create($data);
        return back()->with('success', 'Homework added successfully.');
    }

    public function destroy(Request $request, Homework $homework): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('homework'), 403);
        $user = $request->user();

        if (! $user->is_admin && $user->branch_id) {
            abort_unless((int) $homework->branch_id === (int) $user->branch_id, 403);
        }
        if ($user->isClassTeacher()) {
            abort_unless($homework->class_name === $user->assigned_class && (! $user->assigned_section || $homework->section === $user->assigned_section), 403);
        }

        $homework->delete();
        return back()->with('success', 'Homework deleted successfully.');
    }
}
