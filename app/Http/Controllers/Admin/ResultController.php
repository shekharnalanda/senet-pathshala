<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamResult;
use App\Models\SchoolSetting;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResultController extends Controller
{
    public function index(): View
    {
        $students = Student::with('user')->where('is_active', true)->orderBy('admission_no')->get();
        $results = ExamResult::with('student.user')->latest('exam_date')->latest('id')->get();
        $examNames = ExamResult::query()->select('exam_name')->distinct()->orderBy('exam_name')->pluck('exam_name');
        $classNames = Student::query()->whereNotNull('class_name')->where('class_name','!=','')->distinct()->orderBy('class_name')->pluck('class_name');
        return view('admin.results.index', compact('students', 'results', 'examNames', 'classNames'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedResult($request);
        if ((float)$data['marks_obtained'] > (float)$data['max_marks']) {
            return back()->withErrors(['marks_obtained'=>'Marks obtained cannot be greater than maximum marks.'])->withInput();
        }
        ExamResult::updateOrCreate(
            ['student_id'=>$data['student_id'],'exam_name'=>$data['exam_name'],'subject'=>$data['subject']],
            ['max_marks'=>$data['max_marks'],'marks_obtained'=>$data['marks_obtained'],'exam_date'=>$data['exam_date']??null,'remark'=>$data['remark']??null]
        );
        return back()->with('success','Result saved successfully.');
    }

    public function edit(ExamResult $result): View
    {
        $result->load('student.user');
        return view('admin.results.edit', compact('result'));
    }

    public function update(Request $request, ExamResult $result): RedirectResponse
    {
        $data = $request->validate([
            'exam_name'=>['required','string','max:150'],
            'subject'=>['required','string','max:150'],
            'max_marks'=>['required','numeric','min:0.01'],
            'marks_obtained'=>['required','numeric','min:0'],
            'exam_date'=>['nullable','date'],
            'remark'=>['nullable','string','max:255'],
        ]);
        if ((float)$data['marks_obtained'] > (float)$data['max_marks']) {
            return back()->withErrors(['marks_obtained'=>'Marks obtained cannot be greater than maximum marks.'])->withInput();
        }
        $result->update($data);
        return redirect()->route('admin.results.index')->with('success','Result updated successfully.');
    }

    public function reportCard(Student $student, string $exam): View
    {
        $student->load('user');
        $results = ExamResult::where('student_id',$student->id)->where('exam_name',$exam)->orderBy('subject')->get();
        abort_if($results->isEmpty(),404);
        $summary = $this->summary($results);
        $school = SchoolSetting::first();
        return view('admin.results.report-card', compact('student','results','exam','school','summary'));
    }

    public function bulkReportCards(Request $request): View
    {
        $data = $request->validate([
            'exam'=>['required','string','max:150'],
            'class_name'=>['nullable','string','max:100'],
        ]);
        $studentIds = ExamResult::where('exam_name',$data['exam'])->distinct()->pluck('student_id');
        $students = Student::with('user')->whereIn('id',$studentIds)
            ->when(!empty($data['class_name']),fn($q)=>$q->where('class_name',$data['class_name']))
            ->orderBy('class_name')->orderBy('section')->orderBy('roll_no')->get();
        $cards = $students->map(function($student) use($data){
            $rows = ExamResult::where('student_id',$student->id)->where('exam_name',$data['exam'])->orderBy('subject')->get();
            return ['student'=>$student,'results'=>$rows,'summary'=>$this->summary($rows)];
        });
        abort_if($cards->isEmpty(),404,'No report cards found for the selected filters.');
        $school = SchoolSetting::first();
        $exam = $data['exam'];
        return view('admin.results.bulk-report-cards',compact('cards','school','exam'));
    }

    public function destroy(ExamResult $result): RedirectResponse
    {
        $result->delete();
        return back()->with('success','Result deleted successfully.');
    }

    private function validatedResult(Request $request): array
    {
        return $request->validate([
            'student_id'=>['required','exists:students,id'],
            'exam_name'=>['required','string','max:150'],
            'subject'=>['required','string','max:150'],
            'max_marks'=>['required','numeric','min:0.01'],
            'marks_obtained'=>['required','numeric','min:0'],
            'exam_date'=>['nullable','date'],
            'remark'=>['nullable','string','max:255'],
        ]);
    }

    private function summary($results): array
    {
        $total=(float)$results->sum(fn($r)=>(float)$r->max_marks);
        $obtained=(float)$results->sum(fn($r)=>(float)$r->marks_obtained);
        $percentage=$total>0?round(($obtained/$total)*100,1):0;
        $grade=$percentage>=90?'A+':($percentage>=80?'A':($percentage>=70?'B+':($percentage>=60?'B':($percentage>=50?'C':($percentage>=40?'D':'F')))));
        return ['total'=>$total,'obtained'=>$obtained,'percentage'=>$percentage,'grade'=>$grade,'result'=>$percentage>=40?'PASS':'FAIL'];
    }
}
