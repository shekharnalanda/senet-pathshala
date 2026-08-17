<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Homework;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeworkController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('homework'), 403);
        $user = $request->user();
        $query = Homework::query();
        if ($user->isClassTeacher()) {
            $query->where('class_name', $user->assigned_class);
            if ($user->assigned_section) $query->where('section', $user->assigned_section);
            $classes = collect([$user->assigned_class]);
        } else {
            $classes = Student::whereNotNull('class_name')->where('class_name', '<>', '')
                ->select('class_name')->distinct()->orderBy('class_name')->pluck('class_name');
        }
        $homeworks = $query->latest('homework_date')->latest('id')->get();
        return view('admin.homework.index', compact('homeworks', 'classes'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('homework'), 403);
        $data = $request->validate([
            'class_name' => ['required', 'string', 'max:100'],
            'section' => ['nullable', 'string', 'max:50'],
            'subject' => ['required', 'string', 'max:150'],
            'homework_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:homework_date'],
            'details' => ['required', 'string', 'max:5000'],
        ]);
        $user = $request->user();
        if ($user->isClassTeacher()) {
            abort_unless($data['class_name'] === $user->assigned_class, 403);
            if ($user->assigned_section) {
                abort_unless(($data['section'] ?? null) === $user->assigned_section, 403);
            }
        }
        Homework::create($data);
        return back()->with('success', 'Homework added successfully.');
    }

    public function destroy(Request $request, Homework $homework): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('homework'), 403);
        $user = $request->user();
        if ($user->isClassTeacher()) {
            abort_unless($homework->class_name === $user->assigned_class && (!$user->assigned_section || $homework->section === $user->assigned_section), 403);
        }
        $homework->delete();
        return back()->with('success', 'Homework deleted successfully.');
    }
}
