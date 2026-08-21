<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->is_admin, 403);

        $branches = Branch::withCount(['students', 'admissionApplications'])
            ->orderByDesc('is_main')
            ->orderBy('name')
            ->get();

        return view('admin.branches.index', compact('branches'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->is_admin, 403);
        $data = $this->validated($request);

        DB::transaction(function () use ($data) {
            if (! empty($data['is_main'])) {
                Branch::where('is_main', true)->update(['is_main' => false]);
            }
            Branch::create($data);
        });

        return back()->with('success', 'Campus created successfully.');
    }

    public function update(Request $request, Branch $branch): RedirectResponse
    {
        abort_unless($request->user()?->is_admin, 403);
        $data = $this->validated($request, $branch);

        DB::transaction(function () use ($data, $branch) {
            if (! empty($data['is_main'])) {
                Branch::where('id', '<>', $branch->id)->where('is_main', true)->update(['is_main' => false]);
            }
            $branch->update($data);
        });

        return back()->with('success', 'Campus updated successfully.');
    }

    private function validated(Request $request, ?Branch $branch = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('branches', 'code')->ignore($branch?->id)],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'is_main' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}
