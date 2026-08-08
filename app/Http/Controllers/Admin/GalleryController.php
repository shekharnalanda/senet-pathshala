<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        $galleries = Gallery::latest()->paginate(20);
        return view('admin.gallery.index', compact('galleries'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
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
