<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StudentDocumentController extends Controller
{
    public function index(): View
    {
        $students = Student::with('user')->where('is_active', true)->orderBy('admission_no')->get();
        $documents = StudentDocument::with('student.user')->latest('id')->get();
        return view('admin.documents.index', compact('students','documents'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required','exists:students,id'],
            'title' => ['required','string','max:150'],
            'document_type' => ['required','string','max:100'],
            'document' => ['required','file','mimes:pdf,jpg,jpeg,png','max:5120'],
            'note' => ['nullable','string','max:255'],
        ]);
        $data['file_path'] = $request->file('document')->store('student-documents','public');
        unset($data['document']);
        StudentDocument::create($data);
        return back()->with('success','Document uploaded successfully.');
    }

    public function destroy(StudentDocument $document): RedirectResponse
    {
        if ($document->file_path) Storage::disk('public')->delete($document->file_path);
        $document->delete();
        return back()->with('success','Document deleted successfully.');
    }
}
