<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
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

        $today = Carbon::today();
        $todayAttendance = Attendance::whereDate('attendance_date', $today)->get();
        $todayPresent = $todayAttendance->where('status', 'Present')->count();
        $todayMarked = $todayAttendance->whereIn('status', ['Present','Absent'])->count();
        $todayAttendancePercentage = $todayMarked > 0 ? round(($todayPresent / $todayMarked) * 100, 1) : 0;

        $payments = FeePayment::all();
        $feeCollected = (float) $payments->sum(fn($p) => (float) $p->amount_paid);
        $studentTotals = $payments->groupBy('student_id')->map(function ($rows) {
            $latest = $rows->sortByDesc('id')->first();
            $total = $latest ? (float) $latest->total_fee : 0;
            $paid = (float) $rows->sum(fn($p) => (float) $p->amount_paid);
            return max(0, $total - $paid);
        });
        $pendingFees = (float) $studentTotals->sum();

        return view('admin.dashboard', [
            'studentCount' => Student::count(),
            'activeStudentCount' => Student::where('is_active', true)->count(),
            'todayAttendancePercentage' => $todayAttendancePercentage,
            'todayPresent' => $todayPresent,
            'feeCollected' => $feeCollected,
            'pendingFees' => $pendingFees,
            'resultCount' => ExamResult::count(),
            'homeworkCount' => Homework::count(),
            'documentCount' => StudentDocument::count(),
            'noticeCount' => Notice::count(),
            'publishedNoticeCount' => Notice::where('status', true)->count(),
            'galleryCount' => Gallery::count(),
            'contactCount' => Contact::count(),
            'recentContacts' => Contact::latest()->take(5)->get(),
        ]);
    }
}
