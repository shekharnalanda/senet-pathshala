<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;use Illuminate\Database\Eloquent\Relations\HasOne;use Illuminate\Foundation\Auth\User as Authenticatable;use Illuminate\Notifications\Notifiable;
class User extends Authenticatable
{
use HasFactory,Notifiable;
protected $fillable=['name','email','password','is_admin','role','assigned_class','assigned_section','permissions'];
protected $hidden=['password','remember_token'];
protected $casts=['email_verified_at'=>'datetime','password'=>'hashed','is_admin'=>'boolean','permissions'=>'array'];
public function student():HasOne{return $this->hasOne(Student::class);}
public function isClassTeacher():bool{return $this->role==='class_teacher';}
public function hasPermission(string $permission):bool{return $this->is_admin||($this->isClassTeacher()&&in_array($permission,$this->permissions??[],true));}
}
