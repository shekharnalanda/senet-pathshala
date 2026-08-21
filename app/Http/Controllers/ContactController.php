<?php

namespace App\Http\Controllers;

use App\Models\Branch;
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
            'branch_id' => ['required', 'exists:branches,id'],
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'message' => ['nullable', 'string', 'max:5000'],
        ]);

        $contact = Contact::create($data);

        if (!empty($data['email'])) {
            try {
                $school = SchoolSetting::first();
                $branch = Branch::find($data['branch_id']);
                $schoolName = $school?->school_name ?: 'C-Net Pathshala';
                $body = "Dear {$data['name']},\n\nWelcome to {$schoolName}. Thank you for contacting us regarding ".($branch?->name ?: 'our campus').". We have received your enquiry and our school team will get back to you soon.\n\nCampus Contact Details:\nPhone: ".($branch?->phone ?: ($school?->phone ?: '—'))."\nEmail: ".($branch?->email ?: ($school?->email ?: '—'))."\nAddress: ".($branch?->address ?: ($school?->address ?: '—'))."\n\nRegards,\n{$schoolName}";
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
