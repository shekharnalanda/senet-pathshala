<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;use Illuminate\Database\Eloquent\Relations\BelongsTo;use Illuminate\Database\Eloquent\Relations\HasOne;use Illuminate\Foundation\Auth\User as Authenticatable;use Illuminate\Notifications\Notifiable;
class User extends Authenticatable
{
use HasFactory,Notifiable;
protected $fillable=['branch_id','name','email','password','is_admin','role','assigned_class','assigned_section','permissions'];
protected $hidden=['password','remember_token'];
protected $casts=['email_verified_at'=>'datetime','password'=>'hashed','is_admin'=>'boolean','permissions'=>'array'];
public function student():HasOne{return $this->hasOne(Student::class);}
public function branch():BelongsTo{return $this->belongsTo(Branch::class);}
public function isClassTeacher():bool{return $this->role==='class_teacher';}
public function isOfficeManager():bool{return $this->role==='office_manager';}
public function isPrincipal():bool{return $this->role==='principal';}
public function isStaff():bool{return in_array($this->role,['class_teacher','office_manager','principal'],true);}
public function hasPermission(string $permission):bool{return $this->is_admin||($this->isStaff()&&in_array($permission,$this->permissions??[],true));}
}
