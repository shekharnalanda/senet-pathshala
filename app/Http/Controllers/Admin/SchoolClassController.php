<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
class SchoolClassController extends Controller
{
    public function index(): View {$classes=SchoolClass::orderBy('sort_order')->orderBy('name')->get();return view('admin.classes.index',compact('classes'));}
    public function store(Request $request): RedirectResponse {$data=$request->validate(['name'=>['required','string','max:100','unique:school_classes,name'],'sections'=>['nullable','string','max:255'],'sort_order'=>['nullable','integer','min:0'],'is_active'=>['nullable','boolean']]);$data['is_active']=(bool)($data['is_active']??false);$data['sort_order']=$data['sort_order']??0;SchoolClass::create($data);return back()->with('success','Class added successfully.');}
    public function update(Request $request,SchoolClass $schoolClass): RedirectResponse {$data=$request->validate(['name'=>['required','string','max:100',Rule::unique('school_classes','name')->ignore($schoolClass->id)],'sections'=>['nullable','string','max:255'],'sort_order'=>['nullable','integer','min:0'],'is_active'=>['nullable','boolean']]);$data['is_active']=(bool)($data['is_active']??false);$data['sort_order']=$data['sort_order']??0;$schoolClass->update($data);return back()->with('success','Class updated successfully.');}
    public function destroy(SchoolClass $schoolClass): RedirectResponse {$schoolClass->delete();return back()->with('success','Class deleted successfully.');}
}
