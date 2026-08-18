<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearningProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LearningProgramController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->is_admin,403);
        $programs=LearningProgram::orderBy('sort_order')->get();
        return view('admin.programs.index',compact('programs'));
    }
    public function update(Request $request,LearningProgram $program): RedirectResponse
    {
        abort_unless($request->user()->is_admin,403);
        $data=$request->validate(['name'=>['required','string','max:100'],'card_text'=>['nullable','string','max:500'],'intro'=>['required','string','max:2000'],'focus'=>['nullable','string','max:5000'],'learning_approach'=>['nullable','string','max:3000'],'image'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:3072'],'is_active'=>['nullable','boolean']]);
        $data['is_active']=$request->boolean('is_active');
        if($request->hasFile('image')){if($program->image&&str_starts_with($program->image,'programs/'))Storage::disk('public')->delete($program->image);$data['image']=$request->file('image')->store('programs','public');}
        $program->update($data);
        return back()->with('success',$program->name.' program updated successfully.');
    }
}
