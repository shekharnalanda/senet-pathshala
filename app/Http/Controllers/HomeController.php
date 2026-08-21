<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\LearningProgram;
use App\Models\Notice;
use App\Models\SchoolSetting;

class HomeController extends Controller
{
    public function index()
    {
        $settings = SchoolSetting::first();
        $notices = Notice::with('branch')->where('status', true)->latest('notice_date')->take(5)->get();
        $galleries = Gallery::latest()->take(8)->get();
        $programs = LearningProgram::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        return view('home', compact('settings', 'notices', 'galleries', 'programs'));
    }
}
