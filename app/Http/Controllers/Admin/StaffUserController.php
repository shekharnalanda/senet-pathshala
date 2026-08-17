<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;use App\Models\SchoolClass;use App\Models\User;use Illuminate\Http\RedirectResponse;use Illuminate\Http\Request;use Illuminate\Support\Facades\Hash;use Illuminate\Validation\Rule;use Illuminate\View\View;
class StaffUserController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->is_admin,403);
        $users=User::whereIn('role',['class_teacher','office_manager','principal'])->orderBy('name')->get();
        $classes=SchoolClass::where('is_active',true)->orderBy('sort_order')->orderBy('name')->get();
        return view('admin.staff-users.index',compact('users','classes'));
    }
    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->is_admin,403);
        $data=$this->validated($request);
        $data['password']=Hash::make($data['password']);$data['is_admin']=false;
        if(($data['role']??'')!=='class_teacher'){$data['assigned_class']=null;$data['assigned_section']=null;}
        User::create($data);return back()->with('success','Staff user created successfully.');
    }
    public function update(Request $request,User $staffUser): RedirectResponse
    {
        abort_unless($request->user()?->is_admin,403);abort_unless($staffUser->isStaff(),404);
        $data=$this->validated($request,$staffUser);if(!empty($data['password']))$data['password']=Hash::make($data['password']);else unset($data['password']);$data['is_admin']=false;
        if(($data['role']??'')!=='class_teacher'){$data['assigned_class']=null;$data['assigned_section']=null;}
        $staffUser->update($data);return back()->with('success','Staff user updated successfully.');
    }
    public function destroy(Request $request,User $staffUser): RedirectResponse
    {
        abort_unless($request->user()?->is_admin,403);abort_unless($staffUser->isStaff(),404);$staffUser->delete();return back()->with('success','Staff user deleted successfully.');
    }
    private function validated(Request $request,?User $user=null): array
    {
        return $request->validate([
            'name'=>['required','string','max:255'],'email'=>['required','email','max:255',Rule::unique('users','email')->ignore($user?->id)],
            'password'=>[$user?'nullable':'required','string','min:6'],'role'=>['required','in:class_teacher,office_manager,principal'],
            'assigned_class'=>['nullable','string','max:100'],'assigned_section'=>['nullable','string','max:50'],
            'permissions'=>['nullable','array'],'permissions.*'=>['in:attendance,homework,students,results,report_cards,certificates,fees'],
        ]);
    }
}
