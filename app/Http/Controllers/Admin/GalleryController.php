<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $query = Gallery::with('branch')->latest();
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        $galleries = $query->paginate(20)->withQueryString();
        $branches = Branch::where('is_active', true)->orderByDesc('is_main')->orderBy('name')->get();
        return view('admin.gallery.index', compact('galleries', 'branches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'branch_id' => ['nullable', 'exists:branches,id'],
            'title' => ['required', 'string', 'max:255'],
            'image' => ['required', 'image', 'max:4096'],
        ]);

        $data['image'] = $request->file('image')->store('gallery', 'public');
        Gallery::create($data);

        return back()->with('success', 'Gallery image uploaded successfully.');
    }

    public function destroy(Gallery $gallery)
    {
        if ($gallery->image) {
            Storage::disk('public')->delete($gallery->image);
        }

        $gallery->delete();
        return back()->with('success', 'Gallery image deleted successfully.');
    }
}
