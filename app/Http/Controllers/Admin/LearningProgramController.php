<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\LearningProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LearningProgramController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->is_admin,403);
        $query=LearningProgram::with('branch')->orderBy('sort_order')->orderBy('name');
        if($request->filled('branch_id')){
            $branchId=$request->integer('branch_id');
            if(Branch::whereKey($branchId)->where('is_active',true)->exists())$query->where('branch_id',$branchId);
        }
        $programs=$query->get();
        $branches=Branch::where('is_active',true)->orderByDesc('is_main')->orderBy('name')->get();
        return view('admin.programs.index',compact('programs','branches'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->is_admin,403);
        $data=$request->validate([
            'branch_id'=>['nullable',Rule::exists('branches','id')->where('is_active',true)],
            'name'=>['required','string','max:100'],
            'slug'=>['nullable','string','max:100','regex:/^[a-z0-9-]+$/',Rule::unique('learning_programs','slug')],
            'card_text'=>['nullable','string','max:500'],
            'intro'=>['required','string','max:2000'],
            'focus'=>['nullable','string','max:5000'],
            'learning_approach'=>['nullable','string','max:3000'],
            'image'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:3072'],
            'sort_order'=>['nullable','integer','min:0','max:999'],
            'is_active'=>['nullable','boolean'],
        ]);
        $slug=$data['slug']??Str::slug($data['name']);
        if(LearningProgram::where('slug',$slug)->exists())return back()->withErrors(['slug'=>'This program URL/slug already exists.'])->withInput();
        $data['slug']=$slug;
        $data['is_active']=$request->boolean('is_active');
        $data['sort_order']=$data['sort_order']??((int)LearningProgram::max('sort_order')+1);
        if($request->hasFile('image'))$data['image']=$request->file('image')->store('programs','public');
        LearningProgram::create($data);
        return back()->with('success','New learning program added successfully.');
    }

    public function update(Request $request,LearningProgram $program): RedirectResponse
    {
        abort_unless($request->user()->is_admin,403);
        $data=$request->validate([
            'branch_id'=>['nullable',Rule::exists('branches','id')->where('is_active',true)],
            'name'=>['required','string','max:100'],
            'slug'=>['required','string','max:100','regex:/^[a-z0-9-]+$/',Rule::unique('learning_programs','slug')->ignore($program->id)],
            'card_text'=>['nullable','string','max:500'],
            'intro'=>['required','string','max:2000'],
            'focus'=>['nullable','string','max:5000'],
            'learning_approach'=>['nullable','string','max:3000'],
            'image'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:3072'],
            'sort_order'=>['nullable','integer','min:0','max:999'],
            'is_active'=>['nullable','boolean'],
        ]);
        $data['is_active']=$request->boolean('is_active');
        if($request->hasFile('image')){
            if($program->image&&str_starts_with($program->image,'programs/'))Storage::disk('public')->delete($program->image);
            $data['image']=$request->file('image')->store('programs','public');
        }
        $program->update($data);
        return back()->with('success',$program->name.' program updated successfully.');
    }

    public function destroy(Request $request,LearningProgram $program): RedirectResponse
    {
        abort_unless($request->user()->is_admin,403);
        if($program->image&&str_starts_with($program->image,'programs/'))Storage::disk('public')->delete($program->image);
        $name=$program->name;
        $program->delete();
        return back()->with('success',$name.' program removed successfully.');
    }
}
