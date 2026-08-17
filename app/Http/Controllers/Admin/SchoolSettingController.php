<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolSettingController extends Controller
{
    public function edit(Request $request)
    {
        abort_unless($request->user()->is_admin,403);
        $settings = SchoolSetting::firstOrCreate([], ['school_name'=>'C-Net Pathshala']);
        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        abort_unless($request->user()->is_admin,403);
        $data = $request->validate([
            'school_name'=>['required','string','max:255'],'phone'=>['nullable','string','max:30'],'email'=>['nullable','email','max:255'],
            'address'=>['nullable','string'],'website'=>['nullable','url','max:255'],'academic_session'=>['required','regex:/^\d{4}-\d{2}$/','max:7'],
            'admission_text'=>['nullable','string','max:255'],'footer_about'=>['nullable','string','max:1000'],'main_campus'=>['nullable','string','max:1000'],
            'branch_office'=>['nullable','string','max:1000'],'footer_bottom_text'=>['nullable','string','max:255'],
            'home_heading'=>['nullable','string','max:255'],'home_subheading'=>['nullable','string','max:1000'],
            'home_hero_image'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:5120'],
            'about_heading'=>['nullable','string','max:255'],'about_content'=>['nullable','string','max:5000'],
            'gallery_heading'=>['nullable','string','max:255'],'gallery_subheading'=>['nullable','string','max:1000'],
        ], ['academic_session.regex'=>'Academic session must be like 2026-27.']);

        $settings = SchoolSetting::firstOrCreate([]);
        if ($request->hasFile('home_hero_image')) {
            if ($settings->home_hero_image) Storage::disk('public')->delete($settings->home_hero_image);
            $data['home_hero_image'] = $request->file('home_hero_image')->store('website','public');
        } else {
            unset($data['home_hero_image']);
        }
        $settings->update($data);
        return back()->with('success','Website and school settings updated successfully.');
    }
}
