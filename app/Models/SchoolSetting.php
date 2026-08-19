<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    protected $fillable = [
        'school_name','logo','phone','email','address','website','academic_session',
        'admission_text','footer_about','main_campus','branch_office','footer_bottom_text',
        'home_heading','home_subheading','home_hero_image','about_heading','about_content',
        'about_hero_image','about_image_one','about_image_two',
        'admission_hero_image','admission_image_one','admission_image_two',
        'gallery_heading','gallery_subheading','gallery_hero_image',
    ];
}
