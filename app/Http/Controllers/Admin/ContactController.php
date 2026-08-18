<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::latest()->paginate(20);
        return view('admin.contacts.index', compact('contacts'));
    }

    public function reply(Request $request, Contact $contact)
    {
        abort_if(empty($contact->email), 422, 'This enquiry does not have an email address.');
        $data = $request->validate(['reply_message' => ['required','string','max:5000']]);
        $school = SchoolSetting::first();
        $schoolName = $school?->school_name ?: 'C-Net Pathshala';
        $body = $data['reply_message']."\n\nRegards,\n{$schoolName}\nPhone: ".($school?->phone ?: '—')."\nEmail: ".($school?->email ?: '—')."\nAddress: ".($school?->address ?: '—');
        try {
            Mail::raw($body, function ($message) use ($contact, $schoolName) {
                $message->to($contact->email)->subject($schoolName.' - Reply to your enquiry');
            });
        } catch (\Throwable $e) {
            return back()->withErrors(['reply_message' => 'Email could not be sent. Please check SMTP/Mail settings. '.$e->getMessage()]);
        }
        return back()->with('success', 'Reply email sent successfully to '.$contact->email.'.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return back()->with('success', 'Enquiry deleted successfully.');
    }
}
