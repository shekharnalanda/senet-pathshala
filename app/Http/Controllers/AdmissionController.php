<?php
namespace App\Http\Controllers;
use App\Models\AdmissionApplication;
use App\Models\Branch;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Services\CentralSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
class AdmissionController extends Controller
{
 public function store(Request $request, CentralSyncService $centralSync){$data=$request->validate(['branch_id'=>'required|exists:branches,id','student_name'=>'required|string|max:150','date_of_birth'=>'nullable|date','gender'=>'nullable|in:Male,Female,Other','class_applied'=>'required|string|max:100','father_name'=>'nullable|string|max:150','mother_name'=>'nullable|string|max:150','guardian_name'=>'nullable|string|max:150','phone'=>'required|string|max:30','alternate_phone'=>'nullable|string|max:30','email'=>'nullable|email|max:150','address'=>'nullable|string|max:1000','previous_school'=>'nullable|string|max:200','message'=>'nullable|string|max:1500']);
 $branch=Branch::whereKey($data['branch_id'])->where('is_active',true)->first();if(!$branch)throw ValidationException::withMessages(['branch_id'=>'The selected campus is not currently accepting applications.']);
 $classAllowed=SchoolClass::where('is_active',true)->where('name',$data['class_applied'])->where(function($q)use($data){$q->whereNull('branch_id')->orWhere('branch_id',$data['branch_id']);})->exists();if(!$classAllowed)throw ValidationException::withMessages(['class_applied'=>'The selected class is not available at this campus.']);
 $data['application_no']='ADM-'.now()->format('Ymd').'-'.strtoupper(substr(uniqid(),-6));$data['status']='pending';$application=AdmissionApplication::create($data);
 $centralSync->admission(['business_code'=>config('services.mci_central.business_code'),'source_reference_id'=>'pathshala-admission-'.$application->application_no,'source_site'=>config('app.url','https://cnet.mciedu.in'),'application_reference'=>$application->application_no,'applicant_name'=>$application->student_name,'phone'=>$application->phone,'email'=>$application->email,'course_program'=>$application->class_applied,'status'=>$application->status,'submitted_at'=>($application->created_at?:now())->toIso8601String(),'metadata'=>['branch_id'=>$branch?->id,'branch_name'=>$branch?->name,'father_name'=>$application->father_name,'mother_name'=>$application->mother_name,'guardian_name'=>$application->guardian_name,'previous_school'=>$application->previous_school]]);
 if(!empty($data['email'])){try{$school=SchoolSetting::first();$schoolName=$school?->school_name?:'C-Net Pathshala';$body="Dear Parent/Guardian,\n\nWelcome to {$schoolName}. We have received the online admission application for {$data['student_name']}.\nApplication No: {$application->application_no}\nCampus: ".($branch?->name?:'—')."\nClass Applied: {$data['class_applied']}\n\nThe application is currently under review. The school office will contact you after verification.\n\nCampus Contact Details:\nPhone: ".($branch?->phone?:($school?->phone?:'—'))."\nEmail: ".($branch?->email?:($school?->email?:'—'))."\nAddress: ".($branch?->address?:($school?->address?:'—'))."\n\nRegards,\n{$schoolName}";Mail::raw($body,function($message)use($data,$schoolName){$message->to($data['email'])->subject('Welcome to '.$schoolName.' - Admission Application Received');});}catch(\Throwable $e){Log::warning('Admission auto reply failed: '.$e->getMessage());}}
 return back()->with('admission_success','Application submitted successfully. Application No: '.$application->application_no);}
}
