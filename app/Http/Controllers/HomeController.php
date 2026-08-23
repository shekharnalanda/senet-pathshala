<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Gallery;
use App\Models\LearningProgram;
use App\Models\Notice;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $settings = SchoolSetting::first();
        $branches = Branch::where('is_active', true)->orderByDesc('is_main')->orderBy('name')->get();
        $selectedBranchId = $request->filled('branch_id') ? $request->integer('branch_id') : null;

        if ($selectedBranchId && ! $branches->contains('id', $selectedBranchId)) {
            $selectedBranchId = null;
        }

        $notices = Notice::with('branch')
            ->where('status', true)
            ->where(fn ($q) => $q->whereNull('branch_id')->orWhereHas('branch', fn ($branch) => $branch->where('is_active', true)))
            ->when($selectedBranchId, fn ($q) => $q->where(fn ($x) => $x->whereNull('branch_id')->orWhere('branch_id', $selectedBranchId)))
            ->latest('notice_date')
            ->take(5)
            ->get();

        $galleries = Gallery::with('branch')
            ->where(fn ($q) => $q->whereNull('branch_id')->orWhereHas('branch', fn ($branch) => $branch->where('is_active', true)))
            ->when($selectedBranchId, fn ($q) => $q->where(fn ($x) => $x->whereNull('branch_id')->orWhere('branch_id', $selectedBranchId)))
            ->latest()
            ->take(8)
            ->get();

        $programs = LearningProgram::with('branch')
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('branch_id')->orWhereHas('branch', fn ($branch) => $branch->where('is_active', true)))
            ->when($selectedBranchId, fn ($q) => $q->where(fn ($x) => $x->whereNull('branch_id')->orWhere('branch_id', $selectedBranchId)))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $selectedBranch = $selectedBranchId ? $branches->firstWhere('id', $selectedBranchId) : null;

        return view('home', compact('settings', 'notices', 'galleries', 'programs', 'branches', 'selectedBranch', 'selectedBranchId'));
    }

    public function admission(Request $request)
    {
        $branches = Branch::where('is_active', true)->orderByDesc('is_main')->orderBy('name')->get();
        $branchId = $request->filled('branch_id') ? $request->integer('branch_id') : null;

        if ($branchId && ! $branches->contains('id', $branchId)) {
            $branchId = null;
        }

        $classes = SchoolClass::where('is_active', true)
            ->when($branchId, fn ($q) => $q->where(fn ($x) => $x->whereNull('branch_id')->orWhere('branch_id', $branchId)))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admission', compact('classes', 'branches', 'branchId'));
    }

    public function gallery(Request $request)
    {
        $branches = Branch::where('is_active', true)->orderByDesc('is_main')->orderBy('name')->get();
        $branchId = $request->filled('branch_id') ? $request->integer('branch_id') : null;

        if ($branchId && ! $branches->contains('id', $branchId)) {
            $branchId = null;
        }

        $galleries = Gallery::with('branch')
            ->where(fn ($q) => $q->whereNull('branch_id')->orWhereHas('branch', fn ($branch) => $branch->where('is_active', true)))
            ->when($branchId, fn ($q) => $q->where(fn ($x) => $x->whereNull('branch_id')->orWhere('branch_id', $branchId)))
            ->latest()
            ->get();

        return view('gallery', compact('galleries', 'branches', 'branchId'));
    }
}
