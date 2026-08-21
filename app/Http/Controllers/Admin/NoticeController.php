<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->is_admin, 403);
        $query = Notice::with('branch')->latest('notice_date');
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->integer('branch_id'));
        }
        $branches = Branch::where('is_active', true)->orderByDesc('is_main')->orderBy('name')->get();
        $notices = $query->paginate(15)->withQueryString();
        return view('admin.notices.index', compact('notices', 'branches'));
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->is_admin, 403);
        $data = $request->validate([
            'branch_id' => ['nullable', 'exists:branches,id'],
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
        abort_unless($request->user()->is_admin, 403);
        $data = $request->validate([
            'branch_id' => ['nullable', 'exists:branches,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'notice_date' => ['required', 'date'],
            'status' => ['nullable', 'boolean'],
        ]);

        $notice->update($data);

        return back()->with('success', 'Notice updated successfully.');
    }

    public function destroy(Request $request, Notice $notice)
    {
        abort_unless($request->user()->is_admin, 403);
        $notice->delete();
        return back()->with('success', 'Notice deleted successfully.');
    }
}
