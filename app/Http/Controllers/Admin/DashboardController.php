<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Gallery;
use App\Models\Notice;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'noticeCount' => Notice::count(),
            'publishedNoticeCount' => Notice::where('status', true)->count(),
            'galleryCount' => Gallery::count(),
            'contactCount' => Contact::count(),
            'recentContacts' => Contact::latest()->take(5)->get(),
        ]);
    }
}
