<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\SchoolSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'message' => ['nullable', 'string', 'max:5000'],
        ]);

        Contact::create($data);

        if (!empty($data['email'])) {
            try {
                $school = SchoolSetting::first();
                $schoolName = $school?->school_name ?: 'C-Net Pathshala';
                $body = "Dear {$data['name']},\n\nWelcome to {$schoolName}. Thank you for contacting us. We have received your enquiry and our school team will get back to you soon.\n\nSchool Contact Details:\nPhone: ".($school?->phone ?: '—')."\nEmail: ".($school?->email ?: '—')."\nAddress: ".($school?->address ?: '—')."\n\nRegards,\n{$schoolName}";
                Mail::raw($body, function ($message) use ($data, $schoolName) {
                    $message->to($data['email'])->subject('Welcome to '.$schoolName.' - Enquiry Received');
                });
            } catch (\Throwable $e) {
                Log::warning('Enquiry auto reply failed: '.$e->getMessage());
            }
        }

        return back()->with('success', 'Thank you. Your enquiry has been submitted successfully.');
    }
}
