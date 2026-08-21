<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Contact;
use App\Models\ExamResult;
use App\Models\FeePayment;
use App\Models\Gallery;
use App\Models\Homework;
use App\Models\Notice;
use App\Models\Student;
use App\Models\StudentDocument;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function index(): \Illuminate\View\View|RedirectResponse
    {
        $user = request()->user();

        if ($user && ! $user->is_admin) {
            if ($user->hasPermission('fees')) return redirect()->route('admin.fees.index');
            if ($user->hasPermission('certificates')) return redirect()->route('admin.documents.index');
            if ($user->hasPermission('report_cards') || $user->hasPermission('results')) return redirect()->route('admin.results.index');
            if ($user->hasPermission('attendance')) return redirect()->route('admin.attendance.index');
            if ($user->hasPermission('homework')) return redirect()->route('admin.homework.index');
            if ($user->hasPermission('students')) return redirect()->route('admin.students.index');
            abort(403, 'No management permissions have been assigned to this account.');
        }

        $branches = Branch::where('is_active', true)->orderByDesc('is_main')->orderBy('name')->get();
        $branchId = request()->filled('branch_id') ? request()->integer('branch_id') : null;
        $selectedBranch = $branchId ? $branches->firstWhere('id', $branchId) : null;
        if ($branchId && ! $selectedBranch) {
            $branchId = null;
        }

        $studentQuery = Student::query()->when($branchId, fn($q) => $q->where('branch_id', $branchId));
        $studentCount = (clone $studentQuery)->count();
        $activeStudentCount = (clone $studentQuery)->where('is_active', true)->count();

        $today = Carbon::today();
        $todayAttendance = Attendance::whereDate('attendance_date', $today)
            ->when($branchId, fn($q) => $q->whereHas('student', fn($student) => $student->where('branch_id', $branchId)))
            ->get();
        $todayPresent = $todayAttendance->where('status', 'Present')->count();
        $todayMarked = $todayAttendance->whereIn('status', ['Present','Absent'])->count();
        $todayAttendancePercentage = $todayMarked > 0 ? round(($todayPresent / $todayMarked) * 100, 1) : 0;

        $payments = FeePayment::with('student')
            ->when($branchId, fn($q) => $q->whereHas('student', fn($student) => $student->where('branch_id', $branchId)))
            ->get();
        $feeCollected = (float) $payments->sum(fn($p) => (float) $p->amount_paid);
        $studentTotals = $payments->groupBy('student_id')->map(function ($rows) {
            $latest = $rows->sortByDesc('id')->first();
            $total = $latest ? (float) $latest->total_fee : 0;
            $paid = (float) $rows->sum(fn($p) => (float) $p->amount_paid);
            return max(0, $total - $paid);
        });
        $pendingFees = (float) $studentTotals->sum();

        $resultCount = ExamResult::when($branchId, fn($q) => $q->whereHas('student', fn($student) => $student->where('branch_id', $branchId)))->count();
        $homeworkCount = Homework::when($branchId, fn($q) => $q->where(function($h) use ($branchId) { $h->whereNull('branch_id')->orWhere('branch_id', $branchId); }))->count();
        $documentCount = StudentDocument::when($branchId, fn($q) => $q->whereHas('student', fn($student) => $student->where('branch_id', $branchId)))->count();
        $noticeCount = Notice::when($branchId, fn($q) => $q->where(function($n) use ($branchId) { $n->whereNull('branch_id')->orWhere('branch_id', $branchId); }))->count();
        $publishedNoticeCount = Notice::where('status', true)->when($branchId, fn($q) => $q->where(function($n) use ($branchId) { $n->whereNull('branch_id')->orWhere('branch_id', $branchId); }))->count();
        $galleryCount = Gallery::when($branchId, fn($q) => $q->where(function($g) use ($branchId) { $g->whereNull('branch_id')->orWhere('branch_id', $branchId); }))->count();
        $contactQuery = Contact::with('branch')->when($branchId, fn($q) => $q->where('branch_id', $branchId));
        $contactCount = (clone $contactQuery)->count();
        $recentContacts = (clone $contactQuery)->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'branches','selectedBranch','branchId','studentCount','activeStudentCount','todayAttendancePercentage','todayPresent',
            'feeCollected','pendingFees','resultCount','homeworkCount','documentCount','noticeCount','publishedNoticeCount',
            'galleryCount','contactCount','recentContacts'
        ));
    }
}
