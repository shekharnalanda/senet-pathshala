<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdmissionApplication;
use App\Models\Branch;
use App\Models\MciFeeInvoice;
use App\Models\MciPayOrder;
use App\Models\Student;
use App\Services\MciPayClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MciFeeInvoiceController extends Controller
{
    private function guard(Request $request): void
    {
        abort_unless($request->user()?->hasPermission('fees'), 403);
        if (! $request->user()->is_admin) {
            abort_unless($request->user()->branch_id && Branch::whereKey($request->user()->branch_id)->where('is_active', true)->exists(), 403);
        }
    }
    private function students(Request $request)
    {
        $user = $request->user();
        return Student::with('user')->when(! $user->is_admin, fn ($q) => $q->where('branch_id', $user->branch_id))
            ->when($user->isClassTeacher(), fn ($q) => $q->where('class_name', $user->assigned_class)->when($user->assigned_section, fn ($s) => $s->where('section', $user->assigned_section)));
    }
    public function index(Request $request)
    {
        $this->guard($request); $user = $request->user();
        $students = $this->students($request)->where('is_active', true)->get();
        $admissions = $user->is_admin ? AdmissionApplication::where('status', '!=', 'rejected')->latest()->get() : collect();
        $invoices = MciFeeInvoice::when(! $user->is_admin, fn ($q) => $q->where('branch_id', $user->branch_id))
            ->when($user->isClassTeacher(), fn ($q) => $q->where('principal_type', 'student')->whereIn('principal_id', $students->pluck('id')))->latest()->paginate(30);
        return view('mci-pay.invoices', compact('students', 'admissions', 'invoices'));
    }
    public function store(Request $request)
    {
        $this->guard($request);
        $data = $request->validate(['subject' => ['required', 'regex:/^(student|admission):[0-9]+$/'], 'purpose' => ['required', 'string', 'max:150'],
            'billing_month' => ['required', 'date_format:Y-m'], 'amount' => ['required', 'numeric', 'min:1', 'max:9999999.99', 'decimal:0,2']]);
        [$type, $id] = explode(':', $data['subject']);
        if ($type === 'student') { $record = $this->students($request)->where('is_active', true)->findOrFail($id); }
        else { abort_unless($request->user()->is_admin, 403); $record = AdmissionApplication::where('status', '!=', 'rejected')->findOrFail($id); }
        $key = hash('sha256', $type.':'.$id.':'.$data['billing_month'].':'.mb_strtolower(trim($data['purpose'])));
        if (MciFeeInvoice::where('billing_key', $key)->exists()) { throw ValidationException::withMessages(['amount' => 'इस विद्यार्थी, महीने और शुल्क का बिल पहले से बना है।']); }
        MciFeeInvoice::create(['branch_id' => $record->branch_id, 'principal_type' => $type, 'principal_id' => $id,
            'purpose' => trim($data['purpose']), 'billing_month' => $data['billing_month'], 'amount_paise' => (int) round((float) $data['amount'] * 100),
            'billing_key' => $key, 'created_by' => $request->user()->id]);
        return back()->with('success', 'नया फीस बिल बना दिया गया है। विद्यार्थी Pay Fee में इसे देख सकता है।');
    }
    public function link(Request $request, MciFeeInvoice $invoice, MciPayClient $client)
    {
        abort_unless($request->user()?->is_admin && $invoice->principal_type === 'admission', 403);
        $data = $request->validate(['student_id' => ['required', 'integer']]);
        DB::transaction(function () use ($invoice, $data, $client): void {
            $orders = MciPayOrder::where('principal_type','admission')->where('principal_id',$invoice->principal_id)->orderBy('id')->lockForUpdate()->get();
            $locked = MciFeeInvoice::lockForUpdate()->findOrFail($invoice->id);
            abort_unless(! $locked->fee_payment_id && ! $locked->linked_student_id, 409, 'This admission bill is already linked.');
            $student = Student::where('branch_id', $locked->branch_id)->where('is_active', true)->findOrFail($data['student_id']);
            $locked->update(['linked_student_id' => $student->id]);
            $orders->whereIn('status',['verified','needs_review'])->filter(fn ($o) => (int) ($o->metadata['invoice_id'] ?? 0) === (int) $locked->id)->each(fn ($o) => $client->applyVerified($o));
        },3);
        return back()->with('success', 'एडमिशन का भुगतान चुने गए विद्यार्थी के फीस खाते से जोड़ दिया गया है।');
    }
}
