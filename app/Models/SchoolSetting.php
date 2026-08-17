<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    protected $fillable = [
        'school_name','logo','phone','email','address','website','academic_session',
        'admission_text','footer_about','main_campus','branch_office','footer_bottom_text',
        'home_heading','home_subheading','home_hero_image','about_heading','about_content','gallery_heading','gallery_subheading',
    ];
}
