<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function index()
    {
        $notices = Notice::latest('notice_date')->paginate(15);
        return view('admin.notices.index', compact('notices'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'notice_date' => ['required', 'date'],
            'status' => ['nullable', 'boolean'],
        ]);

        Notice::create($data);

        return back()->with('success', 'Notice created successfully.');
    }

    public function update(Request $request, Notice $notice)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'notice_date' => ['required', 'date'],
            'status' => ['nullable', 'boolean'],
        ]);

        $notice->update($data);

        return back()->with('success', 'Notice updated successfully.');
    }

    public function destroy(Notice $notice)
    {
        $notice->delete();
        return back()->with('success', 'Notice deleted successfully.');
    }
}
