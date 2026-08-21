<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AdmissionApplication;
use App\Models\Branch;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
class AdmissionApplicationController extends Controller
{
 public function index(Request $request){$query=AdmissionApplication::with('branch')->latest();if($request->filled('status'))$query->where('status',$request->status);if($request->filled('branch_id'))$query->where('branch_id',$request->branch_id);$branches=Branch::where('is_active',true)->orderByDesc('is_main')->orderBy('name')->get();$applications=$query->paginate(20)->withQueryString();return view('admin.admissions.index',compact('applications','branches'));}
 public function show(AdmissionApplication $admissionApplication){$admissionApplication->load('branch');return view('admin.admissions.show',compact('admissionApplication'));}
 public function updateStatus(Request $request,AdmissionApplication $admissionApplication){$data=$request->validate(['status'=>'required|in:pending,approved,rejected','admin_note'=>'nullable|string|max:2000']);$admissionApplication->update(['status'=>$data['status'],'admin_note'=>$data['admin_note']??null,'reviewed_at'=>$data['status']==='pending'?null:now()]);return redirect()->route('admin.admission-applications.show',$admissionApplication)->with('success','Admission application status updated successfully.');}
 public function reply(Request $request,AdmissionApplication $admissionApplication){abort_if(empty($admissionApplication->email),422,'This application does not have an email address.');$data=$request->validate(['reply_message'=>['required','string','max:5000']]);$school=SchoolSetting::first();$schoolName=$school?->school_name?:'C-Net Pathshala';$body=$data['reply_message']."\n\nApplication No: {$admissionApplication->application_no}\n\nRegards,\n{$schoolName}\nPhone: ".($school?->phone?:'—')."\nEmail: ".($school?->email?:'—')."\nAddress: ".($school?->address?:'—');try{Mail::raw($body,function($message)use($admissionApplication,$schoolName){$message->to($admissionApplication->email)->subject($schoolName.' - Admission Application Reply');});}catch(\Throwable $e){return back()->withErrors(['reply_message'=>'Email could not be sent. Please check SMTP/Mail settings. '.$e->getMessage()]);}return back()->with('success','Reply email sent successfully to '.$admissionApplication->email.'.');}
}
