<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Contact;
use App\Models\SchoolSetting;
use App\Services\CentralSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    public function store(Request $request, CentralSyncService $centralSync): RedirectResponse
    {
        $data = $request->validate([
            'branch_id' => ['nullable', 'exists:branches,id'],
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'message' => ['nullable', 'string', 'max:5000'],
        ]);

        if (!empty($data['branch_id'])) {
            $branch = Branch::whereKey($data['branch_id'])->where('is_active', true)->first();
            if (!$branch) {
                throw ValidationException::withMessages(['branch_id' => 'The selected campus is not currently available for enquiries.']);
            }
        } else {
            $branch = Branch::where('is_main', true)->where('is_active', true)->first()
                ?: Branch::where('is_active', true)->orderByDesc('is_main')->orderBy('name')->first();
            $data['branch_id'] = $branch?->id;
        }

        $contact = Contact::create($data);

        $centralSync->enquiry([
            'business_code' => config('services.mci_central.business_code'),
            'source_reference_id' => 'pathshala-enquiry-'.$contact->id,
            'source_site' => config('app.url', 'https://cnet.mciedu.in'),
            'name' => $contact->name,
            'phone' => $contact->mobile,
            'email' => $contact->email,
            'subject' => 'C-Net Pathshala Enquiry',
            'message' => $contact->message ?: 'General enquiry',
            'category' => 'general',
            'course_service' => $branch?->name,
            'metadata' => [
                'branch_id' => $branch?->id,
                'branch_name' => $branch?->name,
            ],
        ]);

        if (!empty($data['email'])) {
            try {
                $school = SchoolSetting::first();
                $schoolName = $school?->school_name ?: 'C-Net Pathshala';
                $body = "Dear {$data['name']},\n\nWelcome to {$schoolName}. Thank you for contacting us regarding ".($branch?->name ?: 'our school').". We have received your enquiry and our school team will get back to you soon.\n\nCampus Contact Details:\nPhone: ".($branch?->phone ?: ($school?->phone ?: '—'))."\nEmail: ".($branch?->email ?: ($school?->email ?: '—'))."\nAddress: ".($branch?->address ?: ($school?->address ?: '—'))."\n\nRegards,\n{$schoolName}";
                Mail::raw($body, function ($message) use ($data, $schoolName) {
                    $message->to($data['email'])->subject('Welcome to '.$schoolName.' - Enquiry Received');
                });
            } catch (\Throwable $e) {
                Log::warning('Enquiry auto reply failed: '.$e->getMessage());
            }
        }

        try {
            $school = SchoolSetting::first();
            $schoolName = $school?->school_name ?: 'C-Net Pathshala';
            $officialEmail = config('mail.from.address', 'cnetbiharsharif@gmail.com');
            $adminBody =
                "NEW WEBSITE ENQUIRY\n\n".
                "Name: ".$contact->name."\n".
                "Campus: ".($branch?->name ?: '—')."\n".
                "Mobile: ".$contact->mobile."\n".
                "Email: ".($contact->email ?: '—')."\n".
                "Message: ".($contact->message ?: '—')."\n\n".
                "Please review this enquiry in the ".$schoolName." Admin Panel.";

            Mail::raw($adminBody, function ($message) use ($officialEmail, $contact) {
                $message->to($officialEmail)
                    ->subject('New Enquiry - '.$contact->name);
            });
        } catch (\Throwable $e) {
            Log::warning('Enquiry school notification failed: '.$e->getMessage());
        }

        return back()->with('success', 'Thank you. Your enquiry has been submitted successfully.');
    }
}
