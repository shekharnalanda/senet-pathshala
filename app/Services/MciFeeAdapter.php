<?php
namespace App\Services;

use App\Models\AdmissionApplication;
use App\Models\Branch;
use App\Models\FeePayment;
use App\Models\MciFeeInvoice;
use App\Models\MciPayOrder;
use App\Models\Student;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class MciFeeAdapter
{
    public function admissionAccess(Request $request): void
    {
        $data = $request->validate(['application_no'=>['required','string','max:100'],'phone'=>['required','string','max:20']]);
        $record = AdmissionApplication::where('application_no', trim($data['application_no']))->where('status','!=','rejected')->first();
        $provided = preg_replace('/\D/', '', $data['phone']);
        $stored = preg_replace('/\D/', '', (string)($record?->phone ?? ''));
        if (!$record || strlen($provided)<10 || !hash_equals($stored,$provided)) {
            throw ValidationException::withMessages(['application_no'=>'आवेदन नंबर और दर्ज मोबाइल नंबर का मिलान नहीं हुआ।']);
        }
        $request->session()->regenerate();
        $request->session()->put('mci_pay_admission_id',$record->id);
    }

    private function actor(Request $request): array
    {
        if ($request->user()?->student) {
            $student = $request->user()->student;
            abort_unless($student->is_active && (int) $request->user()->branch_id === (int) $student->branch_id
                && Branch::whereKey($student->branch_id)->where('is_active', true)->exists(), 403);
            return ['type' => 'student', 'id' => (string) $student->id, 'record' => $student];
        }
        if ($request->session()->has('mci_pay_admission_id')) {
            $admission = AdmissionApplication::findOrFail($request->session()->get('mci_pay_admission_id'));
            abort_if($admission->status === 'rejected', 403);
            return ['type' => 'admission', 'id' => (string) $admission->id, 'record' => $admission];
        }
        throw new HttpResponseException(redirect()->route('mci-pay.access'));
    }

    private function due(int $studentId): int
    {
        $total = (float) FeePayment::where('student_id', $studentId)->latest('id')->value('total_fee');
        return max(0, (int) round(($total - (float) FeePayment::where('student_id', $studentId)->sum('amount_paid')) * 100));
    }

    private function choices(array $actor): array
    {
        $rows = MciFeeInvoice::where('principal_type', $actor['type'])->where('principal_id', $actor['id'])
            ->whereNull('fee_payment_id')->orderBy('billing_month')->get()->map(fn ($row) => [
                'key' => 'invoice:'.$row->id, 'label' => $row->purpose.' - '.$row->billing_month,
                'amount_paise' => $row->amount_paise, 'month' => $row->billing_month, 'invoice_id' => $row->id,
            ])->all();
        if ($actor['type'] === 'student' && ($due = $this->due((int) $actor['id'])) >= 100) {
            array_unshift($rows, ['key' => 'balance', 'label' => 'पूर्व निर्धारित बकाया फीस', 'amount_paise' => $due, 'month' => now()->format('Y-m')]);
        }
        return $rows;
    }

    public function page(Request $request): array
    {
        $actor = $this->actor($request); $r = $actor['record'];
        return ['payerName' => $actor['type'] === 'student' ? $r->user->name : $r->student_name,
            'studentReference' => $r->admission_no ?? $r->application_no, 'choices' => $this->choices($actor),
            'emptyMessage' => 'अभी फीस का कोई बकाया बिल नहीं है। नए एडमिशन / महीने की फीस का बिल कार्यालय तय करेगा।',
            'orders' => MciPayOrder::where('principal_type', $actor['type'])->where('principal_id', $actor['id'])->latest()->get()];
    }

    public function quote(Request $request): array
    {
        $actor = $this->actor($request); $r = $actor['record'];
        $choice = collect($this->choices($actor))->firstWhere('key', (string) $request->input('choice'));
        if (! $choice) { throw ValidationException::withMessages(['choice' => 'यह फीस अभी देय नहीं है। पेज दोबारा खोलें।']); }
        return ['branch_id' => $r->branch_id, 'principal_type' => $actor['type'], 'principal_id' => $actor['id'],
            'active_key' => $actor['type'].':'.$actor['id'].':'.$choice['key'],
            'student_reference' => $r->admission_no ?? $r->application_no,
            'payer_name' => $actor['type'] === 'student' ? $r->user->name : $r->student_name,
            'payer_phone' => $r->guardian_phone ?? $r->phone, 'purpose' => $choice['label'], 'billing_month' => $choice['month'],
            'amount_paise' => $choice['amount_paise'], 'metadata' => $choice];
    }

    public function adminQuery(Request $request)
    {
        $user = $request->user(); abort_unless($user && $user->hasPermission('fees'), 403);
        $query = MciPayOrder::query();
        if (! $user->is_admin) {
            abort_unless($user->branch_id && Branch::whereKey($user->branch_id)->where('is_active', true)->exists(), 403);
            $query->where('branch_id', $user->branch_id);
        }
        if ($user->isClassTeacher()) {
            $ids = Student::where('branch_id', $user->branch_id)->where('class_name', $user->assigned_class)
                ->when($user->assigned_section, fn ($q) => $q->where('section', $user->assigned_section))->pluck('id');
            $query->where('principal_type', 'student')->whereIn('principal_id', $ids);
        }
        return $query;
    }

    public function authorize(Request $request, MciPayOrder $order): void
    {
        if ($request->user()?->hasPermission('fees')) { abort_unless($this->adminQuery($request)->whereKey($order->id)->exists(), 403); return; }
        $actor = $this->actor($request);
        abort_unless($order->principal_type === $actor['type'] && $order->principal_id === $actor['id'], 403);
    }

    public function apply(MciPayOrder $order): ?string
    {
        $invoice = isset($order->metadata['invoice_id']) ? MciFeeInvoice::lockForUpdate()->findOrFail($order->metadata['invoice_id']) : null;
        if ($order->principal_type === 'admission' && ! $invoice?->linked_student_id) { return null; }
        $studentId = $order->principal_type === 'student' ? (int) $order->principal_id : $invoice->linked_student_id;
        $student = Student::lockForUpdate()->findOrFail($studentId);
        abort_unless((int) $student->branch_id === (int) $order->branch_id, 409);
        $existing = FeePayment::where('mci_pay_order_id', $order->id)->first();
        if ($existing) { return $existing->receipt_no; }
        if (FeePayment::where('transaction_ref', $order->reference)->exists()) { throw ValidationException::withMessages(['payment' => 'यह UTR पहले से फीस रिकॉर्ड में मौजूद है।']); }
        $totalFee = (float) FeePayment::where('student_id', $studentId)->latest('id')->value('total_fee');
        if ($invoice) {
            if ($invoice->fee_payment_id || $invoice->amount_paise !== $order->amount_paise) { throw ValidationException::withMessages(['payment' => 'फीस बिल का समायोजन कार्यालय जाँचे।']); }
            $totalFee += $invoice->amount_paise / 100;
        } elseif ($order->amount_paise > $this->due($studentId)) {
            throw ValidationException::withMessages(['payment' => 'प्राप्त भुगतान वर्तमान बकाये से अधिक है। कार्यालय समायोजन जाँचे।']);
        }
        $payment = FeePayment::create([
            'student_id' => $studentId, 'total_fee' => $totalFee, 'amount_paid' => $order->amount_paise / 100,
            'payment_date' => $order->payment_date, 'payment_mode' => 'UPI',
            'receipt_no' => 'CNET-FEE-'.now()->format('Ymd').'-'.strtoupper(Str::random(10)),
            'mci_pay_order_id' => $order->id, 'transaction_ref' => $order->reference, 'fee_month' => $order->billing_month,
            'note' => 'UTR '.$order->reference.'; '.$order->purpose.'; MCI Pay '.$order->central_receipt,
        ]);
        if ($invoice) { $invoice->update(['fee_payment_id' => $payment->id]); }
        return $payment->receipt_no;
    }
}
