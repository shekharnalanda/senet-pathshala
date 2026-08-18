<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AdmissionApplication;
use Illuminate\Http\Request;
class AdmissionApplicationController extends Controller
{
 public function index(Request $request){$query=AdmissionApplication::latest();if($request->filled('status'))$query->where('status',$request->status);$applications=$query->paginate(20)->withQueryString();return view('admin.admissions.index',compact('applications'));}
 public function show(AdmissionApplication $admissionApplication){return view('admin.admissions.show',compact('admissionApplication'));}
 public function updateStatus(Request $request,AdmissionApplication $admissionApplication){$data=$request->validate(['status'=>'required|in:pending,approved,rejected','admin_note'=>'nullable|string|max:2000']);$admissionApplication->update(['status'=>$data['status'],'admin_note'=>$data['admin_note']??null,'reviewed_at'=>$data['status']==='pending'?null:now()]);return redirect()->route('admin.admission-applications.show',$admissionApplication)->with('success','Admission application status updated successfully.');}
}
