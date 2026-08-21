<?php
namespace App\Http\Controllers;
use App\Models\LearningProgram;use App\Models\SchoolSetting;use Illuminate\View\View;
class ProgramController extends Controller{public function show(string $program):View{$p=LearningProgram::with('branch')->where('slug',$program)->where('is_active',true)->firstOrFail();$details=['name'=>$p->name,'image'=>$p->image,'intro'=>$p->intro,'focus'=>array_values(array_filter(preg_split('/\r\n|\r|\n/',$p->focus??''))),'learning_approach'=>$p->learning_approach,'branch_id'=>$p->branch_id,'branch_name'=>$p->branch?->name];$settings=SchoolSetting::first();return view('programs.show',compact('details','settings'));}}
