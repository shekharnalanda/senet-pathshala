<?php
namespace App\Http\Controllers;
use App\Models\AdmissionApplication;
use Illuminate\Http\Request;
class AdmissionController extends Controller
{
 public function store(Request $request){$data=$request->validate(['student_name'=>'required|string|max:150','date_of_birth'=>'nullable|date','gender'=>'nullable|in:Male,Female,Other','class_applied'=>'required|string|max:100','father_name'=>'nullable|string|max:150','mother_name'=>'nullable|string|max:150','guardian_name'=>'nullable|string|max:150','phone'=>'required|string|max:30','alternate_phone'=>'nullable|string|max:30','email'=>'nullable|email|max:150','address'=>'nullable|string|max:1000','previous_school'=>'nullable|string|max:200','message'=>'nullable|string|max:1500']);$data['application_no']='ADM-'.now()->format('Ymd').'-'.strtoupper(substr(uniqid(),-6));$data['status']='pending';$application=AdmissionApplication::create($data);return back()->with('admission_success','Application submitted successfully. Application No: '.$application->application_no);}
}
