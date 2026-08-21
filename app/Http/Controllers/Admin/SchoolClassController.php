<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
class SchoolClassController extends Controller
{
    public function index(Request $request): View {abort_unless($request->user()?->is_admin,403);$query=SchoolClass::with('branch')->orderBy('sort_order')->orderBy('name');if($request->filled('branch_id'))$query->where('branch_id',$request->branch_id);$classes=$query->get();$branches=Branch::where('is_active',true)->orderByDesc('is_main')->orderBy('name')->get();return view('admin.classes.index',compact('classes','branches'));}
    public function store(Request $request): RedirectResponse {abort_unless($request->user()?->is_admin,403);$data=$request->validate(['branch_id'=>['nullable','exists:branches,id'],'name'=>['required','string','max:100',Rule::unique('school_classes','name')->where(fn($q)=>$q->where('branch_id',$request->input('branch_id')))],'sections'=>['nullable','string','max:255'],'sort_order'=>['nullable','integer','min:0'],'is_active'=>['nullable','boolean']]);$data['is_active']=(bool)($data['is_active']??false);$data['sort_order']=$data['sort_order']??0;SchoolClass::create($data);return back()->with('success','Class added successfully.');}
    public function update(Request $request,SchoolClass $schoolClass): RedirectResponse {abort_unless($request->user()?->is_admin,403);$data=$request->validate(['branch_id'=>['nullable','exists:branches,id'],'name'=>['required','string','max:100',Rule::unique('school_classes','name')->where(fn($q)=>$q->where('branch_id',$request->input('branch_id')))->ignore($schoolClass->id)],'sections'=>['nullable','string','max:255'],'sort_order'=>['nullable','integer','min:0'],'is_active'=>['nullable','boolean']]);$data['is_active']=(bool)($data['is_active']??false);$data['sort_order']=$data['sort_order']??0;$schoolClass->update($data);return back()->with('success','Class updated successfully.');}
    public function destroy(Request $request,SchoolClass $schoolClass): RedirectResponse {abort_unless($request->user()?->is_admin,403);$schoolClass->delete();return back()->with('success','Class deleted successfully.');}
}
