<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->is_admin, 403);
        $query = Gallery::with('branch')->latest();
        if ($request->filled('branch_id')) {
            $branchId = $request->integer('branch_id');
            if (Branch::whereKey($branchId)->where('is_active', true)->exists()) {
                $query->where('branch_id', $branchId);
            }
        }
        $galleries = $query->paginate(20)->withQueryString();
        $branches = Branch::where('is_active', true)->orderByDesc('is_main')->orderBy('name')->get();
        return view('admin.gallery.index', compact('galleries', 'branches'));
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->is_admin, 403);
        $data = $request->validate([
            'branch_id' => ['nullable', Rule::exists('branches', 'id')->where('is_active', true)],
            'title' => ['required', 'string', 'max:255'],
            'image' => ['required', 'image', 'max:4096'],
        ]);

        $data['image'] = $request->file('image')->store('gallery', 'public');
        Gallery::create($data);

        return back()->with('success', 'Gallery image uploaded successfully.');
    }

    public function destroy(Request $request, Gallery $gallery)
    {
        abort_unless($request->user()->is_admin, 403);
        if ($gallery->image) {
            Storage::disk('public')->delete($gallery->image);
        }

        $gallery->delete();
        return back()->with('success', 'Gallery image deleted successfully.');
    }
}
