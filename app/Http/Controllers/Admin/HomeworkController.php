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
    public function index(): View
    {
        $homeworks = Homework::latest('homework_date')->latest('id')->get();
        $classes = Student::whereNotNull('class_name')->where('class_name', '<>', '')
            ->select('class_name')->distinct()->orderBy('class_name')->pluck('class_name');
        return view('admin.homework.index', compact('homeworks', 'classes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'class_name' => ['required', 'string', 'max:100'],
            'section' => ['nullable', 'string', 'max:50'],
            'subject' => ['required', 'string', 'max:150'],
            'homework_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:homework_date'],
            'details' => ['required', 'string', 'max:5000'],
        ]);
        Homework::create($data);
        return back()->with('success', 'Homework added successfully.');
    }

    public function destroy(Homework $homework): RedirectResponse
    {
        $homework->delete();
        return back()->with('success', 'Homework deleted successfully.');
    }
}
