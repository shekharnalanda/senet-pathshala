<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AdmissionApplication extends Model
{
    protected $fillable=['application_no','student_name','date_of_birth','gender','class_applied','father_name','mother_name','guardian_name','phone','alternate_phone','email','address','previous_school','message','status','admin_note','reviewed_at'];
    protected $casts=['date_of_birth'=>'date','reviewed_at'=>'datetime'];
}
