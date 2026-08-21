<?php
namespace App\Http\Controllers;
use App\Models\AdmissionApplication;
use App\Models\Branch;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
class AdmissionController extends Controller
{
 public function store(Request $request){$data=$request->validate(['branch_id'=>'required|exists:branches,id','student_name'=>'required|string|max:150','date_of_birth'=>'nullable|date','gender'=>'nullable|in:Male,Female,Other','class_applied'=>'required|string|max:100','father_name'=>'nullable|string|max:150','mother_name'=>'nullable|string|max:150','guardian_name'=>'nullable|string|max:150','phone'=>'required|string|max:30','alternate_phone'=>'nullable|string|max:30','email'=>'nullable|email|max:150','address'=>'nullable|string|max:1000','previous_school'=>'nullable|string|max:200','message'=>'nullable|string|max:1500']);
 $branch=Branch::whereKey($data['branch_id'])->where('is_active',true)->first();if(!$branch)throw ValidationException::withMessages(['branch_id'=>'The selected campus is not currently accepting applications.']);
 $classAllowed=SchoolClass::where('is_active',true)->where('name',$data['class_applied'])->where(function($q)use($data){$q->whereNull('branch_id')->orWhere('branch_id',$data['branch_id']);})->exists();if(!$classAllowed)throw ValidationException::withMessages(['class_applied'=>'The selected class is not available at this campus.']);
 $data['application_no']='ADM-'.now()->format('Ymd').'-'.strtoupper(substr(uniqid(),-6));$data['status']='pending';$application=AdmissionApplication::create($data);
 if(!empty($data['email'])){try{$school=SchoolSetting::first();$schoolName=$school?->school_name?:'C-Net Pathshala';$body="Dear Parent/Guardian,\n\nWelcome to {$schoolName}. We have received the online admission application for {$data['student_name']}.\nApplication No: {$application->application_no}\nCampus: ".($branch?->name?:'—')."\nClass Applied: {$data['class_applied']}\n\nThe application is currently under review. The school office will contact you after verification.\n\nCampus Contact Details:\nPhone: ".($branch?->phone?:($school?->phone?:'—'))."\nEmail: ".($branch?->email?:($school?->email?:'—'))."\nAddress: ".($branch?->address?:($school?->address?:'—'))."\n\nRegards,\n{$schoolName}";Mail::raw($body,function($message)use($data,$schoolName){$message->to($data['email'])->subject('Welcome to '.$schoolName.' - Admission Application Received');});}catch(\Throwable $e){Log::warning('Admission auto reply failed: '.$e->getMessage());}}
 return back()->with('admission_success','Application submitted successfully. Application No: '.$application->application_no);}
}
