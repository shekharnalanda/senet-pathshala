<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;

class SchoolSettingController extends Controller
{
    public function edit()
    {
        $settings = SchoolSetting::firstOrCreate([], [
            'school_name' => 'C-Net Pathshala',
        ]);
        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'school_name' => ['required','string','max:255'],
            'phone' => ['nullable','string','max:30'],
            'email' => ['nullable','email','max:255'],
            'address' => ['nullable','string'],
            'website' => ['nullable','url','max:255'],
            'academic_session' => ['required','regex:/^\d{4}-\d{2}$/','max:7'],
        ], ['academic_session.regex' => 'Academic session must be like 2026-27.']);

        $settings = SchoolSetting::firstOrCreate([]);
        $settings->update($data);
        return back()->with('success','School settings updated successfully.');
    }
}
