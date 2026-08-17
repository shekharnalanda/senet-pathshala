<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        $students = Student::with('user')->latest()->get();
        return view('admin.students.index', compact('students'));
    }

    public function show(Student $student): View
    {
        $student->load('user');
        return view('admin.students.show', compact('student'));
    }

    public function create(): View
    {
        return view('admin.students.form', ['student' => new Student()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateStudent($request);

        DB::transaction(function () use ($data, $request) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'is_admin' => false,
            ]);

            $studentData = $this->studentData($data);
            $studentData['user_id'] = $user->id;
            if ($request->hasFile('photo')) {
                $studentData['photo'] = $request->file('photo')->store('students', 'public');
            }
            Student::create($studentData);
        });

        return redirect()->route('admin.students.index')->with('success', 'Student added successfully.');
    }

    public function edit(Student $student): View
    {
        $student->load('user');
        return view('admin.students.form', compact('student'));
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $student->load('user');
        $data = $this->validateStudent($request, $student);

        DB::transaction(function () use ($data, $request, $student) {
            $userData = ['name' => $data['name'], 'email' => $data['email']];
            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }
            $student->user->update($userData);

            $studentData = $this->studentData($data);
            if ($request->hasFile('photo')) {
                if ($student->photo) Storage::disk('public')->delete($student->photo);
                $studentData['photo'] = $request->file('photo')->store('students', 'public');
            }
            $student->update($studentData);
        });

        return redirect()->route('admin.students.index')->with('success', 'Student updated successfully.');
    }

    private function validateStudent(Request $request, ?Student $student = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($student?->user_id)],
            'password' => [$student ? 'nullable' : 'required', 'string', 'min:6'],
            'admission_no' => ['required', 'string', 'max:100', Rule::unique('students', 'admission_no')->ignore($student?->id)],
            'class_name' => ['nullable', 'string', 'max:100'],
            'section' => ['nullable', 'string', 'max:50'],
            'roll_no' => ['nullable', 'string', 'max:50'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'max:30'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'guardian_phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function studentData(array $data): array
    {
        return [
            'admission_no' => $data['admission_no'],
            'class_name' => $data['class_name'] ?? null,
            'section' => $data['section'] ?? null,
            'roll_no' => $data['roll_no'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'gender' => $data['gender'] ?? null,
            'guardian_name' => $data['guardian_name'] ?? null,
            'guardian_phone' => $data['guardian_phone'] ?? null,
            'address' => $data['address'] ?? null,
            'is_active' => (bool)($data['is_active'] ?? false),
        ];
    }
}
