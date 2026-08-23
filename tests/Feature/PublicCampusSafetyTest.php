<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCampusSafetyTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_student_verification_rejects_inactive_campus_student(): void
    {
        $branch = Branch::create([
            'name' => 'Inactive Campus',
            'code' => 'OLDVERIFY',
            'is_main' => false,
            'is_active' => false,
        ]);

        $user = User::create([
            'branch_id' => $branch->id,
            'name' => 'Old Campus Student',
            'email' => 'old.verify@example.test',
            'password' => 'password',
            'is_admin' => false,
        ]);

        Student::create([
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'admission_no' => 'VERIFY-OLD-001',
            'class_name' => 'Class 1',
            'section' => 'A',
            'is_active' => true,
        ]);

        $this->get(route('student.verify', 'VERIFY-OLD-001'))->assertNotFound();
    }

    public function test_public_student_verification_rejects_mismatched_login_campus(): void
    {
        $studentBranch = Branch::create([
            'name' => 'Student Campus',
            'code' => 'STUDENT',
            'is_main' => true,
            'is_active' => true,
        ]);
        $wrongBranch = Branch::create([
            'name' => 'Wrong Campus',
            'code' => 'WRONG',
            'is_main' => false,
            'is_active' => true,
        ]);

        $user = User::create([
            'branch_id' => $wrongBranch->id,
            'name' => 'Mismatch Student',
            'email' => 'mismatch.verify@example.test',
            'password' => 'password',
            'is_admin' => false,
        ]);

        Student::create([
            'user_id' => $user->id,
            'branch_id' => $studentBranch->id,
            'admission_no' => 'VERIFY-MISMATCH-001',
            'class_name' => 'Class 1',
            'section' => 'A',
            'is_active' => true,
        ]);

        $this->get(route('student.verify', 'VERIFY-MISMATCH-001'))->assertNotFound();
    }
}
