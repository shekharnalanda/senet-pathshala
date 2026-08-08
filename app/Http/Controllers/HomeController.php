<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\Notice;
use App\Models\SchoolSetting;

class HomeController extends Controller
{
    public function index()
    {
        $settings = SchoolSetting::first();
        $notices = Notice::where('status', true)->latest('notice_date')->take(5)->get();
        $galleries = Gallery::latest()->take(8)->get();

        return view('home', compact('settings', 'notices', 'galleries'));
    }
}
