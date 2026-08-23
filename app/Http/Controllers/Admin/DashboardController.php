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
            abort_unless($user->branch_id && Branch::whereKey($user->branch_id)->where('is_active', true)->exists(), 403, 'No active campus is assigned to this staff account.');
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

        $activeStudentScope = fn ($q) => $q->whereNotNull('branch_id')->whereHas('branch', fn ($branch) => $branch->where('is_active', true));
        $studentQuery = Student::query()
            ->where($activeStudentScope)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId));
        $studentCount = (clone $studentQuery)->count();
        $activeStudentCount = (clone $studentQuery)->where('is_active', true)->count();

        $today = Carbon::today();
        $todayAttendance = Attendance::whereDate('attendance_date', $today)
            ->whereHas('student', function ($student) use ($branchId) {
                $student->whereNotNull('branch_id')
                    ->whereHas('branch', fn ($branch) => $branch->where('is_active', true));
                if ($branchId) $student->where('branch_id', $branchId);
            })
            ->get();
        $todayPresent = $todayAttendance->where('status', 'Present')->count();
        $todayMarked = $todayAttendance->whereIn('status', ['Present','Absent'])->count();
        $todayAttendancePercentage = $todayMarked > 0 ? round(($todayPresent / $todayMarked) * 100, 1) : 0;

        $payments = FeePayment::with('student')
            ->whereHas('student', function ($student) use ($branchId) {
                $student->whereNotNull('branch_id')
                    ->whereHas('branch', fn ($branch) => $branch->where('is_active', true));
                if ($branchId) $student->where('branch_id', $branchId);
            })->get();
        $feeCollected = (float) $payments->sum(fn($p) => (float) $p->amount_paid);
        $studentTotals = $payments->groupBy('student_id')->map(function ($rows) {
            $latest = $rows->sortByDesc('id')->first();
            $total = $latest ? (float) $latest->total_fee : 0;
            $paid = (float) $rows->sum(fn($p) => (float) $p->amount_paid);
            return max(0, $total - $paid);
        });
        $pendingFees = (float) $studentTotals->sum();

        $resultCount = ExamResult::whereHas('student', function ($student) use ($branchId) {
            $student->whereNotNull('branch_id')->whereHas('branch', fn ($branch) => $branch->where('is_active', true));
            if ($branchId) $student->where('branch_id', $branchId);
        })->count();
        $homeworkCount = Homework::where(function ($q) use ($branchId) {
            if ($branchId) {
                $q->whereNull('branch_id')->orWhere('branch_id', $branchId);
            } else {
                $q->whereNull('branch_id')->orWhereHas('branch', fn ($branch) => $branch->where('is_active', true));
            }
        })->count();
        $documentCount = StudentDocument::whereHas('student', function ($student) use ($branchId) {
            $student->whereNotNull('branch_id')->whereHas('branch', fn ($branch) => $branch->where('is_active', true));
            if ($branchId) $student->where('branch_id', $branchId);
        })->count();
        $noticeCount = Notice::where(function ($q) use ($branchId) {
            if ($branchId) {
                $q->whereNull('branch_id')->orWhere('branch_id', $branchId);
            } else {
                $q->whereNull('branch_id')->orWhereHas('branch', fn ($branch) => $branch->where('is_active', true));
            }
        })->count();
        $publishedNoticeCount = Notice::where('status', true)->where(function ($q) use ($branchId) {
            if ($branchId) {
                $q->whereNull('branch_id')->orWhere('branch_id', $branchId);
            } else {
                $q->whereNull('branch_id')->orWhereHas('branch', fn ($branch) => $branch->where('is_active', true));
            }
        })->count();
        $galleryCount = Gallery::where(function ($q) use ($branchId) {
            if ($branchId) {
                $q->whereNull('branch_id')->orWhere('branch_id', $branchId);
            } else {
                $q->whereNull('branch_id')->orWhereHas('branch', fn ($branch) => $branch->where('is_active', true));
            }
        })->count();
        $contactQuery = Contact::with('branch')->where(function ($q) use ($branchId) {
            if ($branchId) {
                $q->where('branch_id', $branchId);
            } else {
                $q->whereNull('branch_id')->orWhereHas('branch', fn ($branch) => $branch->where('is_active', true));
            }
        });
        $contactCount = (clone $contactQuery)->count();
        $recentContacts = (clone $contactQuery)->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'branches','selectedBranch','branchId','studentCount','activeStudentCount','todayAttendancePercentage','todayPresent',
            'feeCollected','pendingFees','resultCount','homeworkCount','documentCount','noticeCount','publishedNoticeCount',
            'galleryCount','contactCount','recentContacts'
        ));
    }
}
