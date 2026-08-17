<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;use App\Models\SchoolClass;use App\Models\User;use Illuminate\Http\RedirectResponse;use Illuminate\Http\Request;use Illuminate\Support\Facades\Hash;use Illuminate\Validation\Rule;use Illuminate\View\View;
class StaffUserController extends Controller
{
    public function index(): View {$users=User::where('role','class_teacher')->orderBy('name')->get();$classes=SchoolClass::where('is_active',true)->orderBy('sort_order')->orderBy('name')->get();return view('admin.staff-users.index',compact('users','classes'));}
    public function store(Request $request): RedirectResponse {$data=$this->validated($request);$data['password']=Hash::make($data['password']);$data['is_admin']=false;$data['role']='class_teacher';$data['permissions']=$data['permissions']??[];User::create($data);return back()->with('success','Class teacher user created successfully.');}
    public function update(Request $request,User $staffUser): RedirectResponse {abort_if($staffUser->role!=='class_teacher',404);$data=$this->validated($request,$staffUser);if(!empty($data['password']))$data['password']=Hash::make($data['password']);else unset($data['password']);$data['permissions']=$data['permissions']??[];$data['is_admin']=false;$data['role']='class_teacher';$staffUser->update($data);return back()->with('success','Class teacher user updated successfully.');}
    public function destroy(User $staffUser): RedirectResponse {abort_if($staffUser->role!=='class_teacher',404);$staffUser->delete();return back()->with('success','Class teacher user deleted successfully.');}
    private function validated(Request $request,?User $user=null): array{return $request->validate(['name'=>['required','string','max:255'],'email'=>['required','email','max:255',Rule::unique('users','email')->ignore($user?->id)],'password'=>[$user?'nullable':'required','string','min:6'],'assigned_class'=>['required','string','max:100'],'assigned_section'=>['nullable','string','max:50'],'permissions'=>['nullable','array'],'permissions.*'=>['in:attendance,homework,students,results']]);}
}
