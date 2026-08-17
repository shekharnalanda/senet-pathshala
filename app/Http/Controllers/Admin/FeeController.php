<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeePayment;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeeController extends Controller
{
    public function index(): View
    {
        $students = Student::with('user')->orderBy('admission_no')->get();
        $payments = FeePayment::with('student.user')->latest('payment_date')->latest('id')->get();
        $summaries = $payments->groupBy('student_id')->map(function ($rows) {
            $total = (float) $rows->first()->total_fee;
            $paid = (float) $rows->sum('amount_paid');
            return ['total' => $total, 'paid' => $paid, 'pending' => max(0, $total - $paid)];
        });
        return view('admin.fees.index', compact('students', 'payments', 'summaries'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'total_fee' => ['required', 'numeric', 'min:0'],
            'amount_paid' => ['required', 'numeric', 'gt:0'],
            'payment_date' => ['required', 'date'],
            'payment_mode' => ['required', 'in:Cash,UPI,Bank Transfer,Cheque'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $data['receipt_no'] = 'CNET-FEE-' . now()->format('YmdHis') . '-' . random_int(10, 99);
        FeePayment::create($data);

        return back()->with('success', 'Fee payment recorded successfully. Receipt: '.$data['receipt_no']);
    }
}
