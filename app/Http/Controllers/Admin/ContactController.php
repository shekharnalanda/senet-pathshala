<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Contact;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->is_admin, 403);
        $query = Contact::with('branch')->latest();
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        $contacts = $query->paginate(20)->withQueryString();
        $branches = Branch::where('is_active', true)->orderByDesc('is_main')->orderBy('name')->get();
        return view('admin.contacts.index', compact('contacts', 'branches'));
    }

    public function reply(Request $request, Contact $contact)
    {
        abort_unless($request->user()->is_admin, 403);
        abort_if(empty($contact->email), 422, 'This enquiry does not have an email address.');
        $contact->load('branch');
        $data = $request->validate(['reply_message' => ['required','string','max:5000']]);
        $school = SchoolSetting::first();
        $schoolName = $school?->school_name ?: 'C-Net Pathshala';
        $body = $data['reply_message']."\n\nRegards,\n{$schoolName}\nCampus: ".($contact->branch?->name ?: '—')."\nPhone: ".($contact->branch?->phone ?: ($school?->phone ?: '—'))."\nEmail: ".($contact->branch?->email ?: ($school?->email ?: '—'))."\nAddress: ".($contact->branch?->address ?: ($school?->address ?: '—'));
        try {
            Mail::raw($body, function ($message) use ($contact, $schoolName) {
                $message->to($contact->email)->subject($schoolName.' - Reply to your enquiry');
            });
        } catch (\Throwable $e) {
            return back()->withErrors(['reply_message' => 'Email could not be sent. Please check SMTP/Mail settings. '.$e->getMessage()]);
        }
        return back()->with('success', 'Reply email sent successfully to '.$contact->email.'.');
    }

    public function destroy(Request $request, Contact $contact)
    {
        abort_unless($request->user()->is_admin, 403);
        $contact->delete();
        return back()->with('success', 'Enquiry deleted successfully.');
    }
}
