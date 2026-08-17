<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
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
}
