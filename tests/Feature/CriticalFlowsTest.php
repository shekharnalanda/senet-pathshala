<?php

namespace Tests\Feature;

use App\Models\AdmissionApplication;
use App\Models\Branch;
use App\Models\FeePayment;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CriticalFlowsTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_admission_page_renders_with_active_campus_and_class(): void
    {
        $branch = $this->makeBranch('Main Campus', 'MAIN', true);

        SchoolClass::create([
            'branch_id' => $branch->id,
            'name' => 'Class 1',
            'sections' => 'A,B',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->get(route('admission', ['branch_id' => $branch->id]))
            ->assertOk()
            ->assertSee('ONLINE ADMISSION')
            ->assertSee('Main Campus')
            ->assertSee('Class 1');
    }

    public function test_admin_can_open_admission_application_detail_page(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
            'is_admin' => true,
        ]);

        $branch = $this->makeBranch('Main Campus', 'MAIN', true);

        $application = AdmissionApplication::create([
            'branch_id' => $branch->id,
            'application_no' => 'TEST-APP-001',
            'student_name' => 'Demo Applicant',
            'class_applied' => 'Class 1',
            'phone' => '9000000000',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.admission-applications.show', $application))
            ->assertOk()
            ->assertSee('Demo Applicant')
            ->assertSee('Main Campus')
            ->assertSee('Admin Review');
    }

    public function test_inactive_campus_is_not_offered_on_public_admission_page(): void
    {
        $this->makeBranch('Inactive Campus', 'OLD', false);

        $this->get(route('admission'))
            ->assertOk()
            ->assertDontSee('Inactive Campus');
    }

    public function test_staff_student_list_is_restricted_to_assigned_campus(): void
    {
        $main = $this->makeBranch('Main Campus', 'MAIN', true);
        $second = $this->makeBranch('Second Campus', 'SECOND', true);
        $staff = $this->makeStaff($main, ['students']);
        $ownStudent = $this->makeStudent($main, 'Own Campus Student', 'OWN-001');
        $otherStudent = $this->makeStudent($second, 'Other Campus Student', 'OTHER-001');

        $this->actingAs($staff)
            ->get(route('admin.students.index'))
            ->assertOk()
            ->assertSee($ownStudent->user->name)
            ->assertDontSee($otherStudent->user->name);
    }

    public function test_staff_attendance_cannot_be_written_for_another_campus_student(): void
    {
        $main = $this->makeBranch('Main Campus', 'MAIN', true);
        $second = $this->makeBranch('Second Campus', 'SECOND', true);
        $staff = $this->makeStaff($main, ['attendance']);
        $ownStudent = $this->makeStudent($main, 'Own Campus Student', 'OWN-002');
        $otherStudent = $this->makeStudent($second, 'Other Campus Student', 'OTHER-002');
        $date = '2026-08-23';

        $this->actingAs($staff)
            ->post(route('admin.attendance.store'), [
                'attendance_date' => $date,
                'attendance' => [
                    $ownStudent->id => ['status' => 'Present', 'remark' => null],
                    $otherStudent->id => ['status' => 'Absent', 'remark' => 'Must be ignored'],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('attendances', [
            'student_id' => $ownStudent->id,
            'attendance_date' => $date,
            'status' => 'Present',
        ]);
        $this->assertDatabaseMissing('attendances', [
            'student_id' => $otherStudent->id,
            'attendance_date' => $date,
        ]);
    }

    public function test_staff_cannot_open_fee_receipt_from_another_campus(): void
    {
        $main = $this->makeBranch('Main Campus', 'MAIN', true);
        $second = $this->makeBranch('Second Campus', 'SECOND', true);
        $staff = $this->makeStaff($main, ['fees']);
        $otherStudent = $this->makeStudent($second, 'Other Fee Student', 'OTHER-FEE-001');

        $payment = FeePayment::create([
            'student_id' => $otherStudent->id,
            'total_fee' => 12000,
            'amount_paid' => 1000,
            'payment_date' => '2026-08-23',
            'payment_mode' => 'Cash',
            'receipt_no' => 'TEST-RECEIPT-001',
        ]);

        $this->actingAs($staff)
            ->get(route('admin.fees.receipt', $payment))
            ->assertForbidden();
    }

    private function makeBranch(string $name, string $code, bool $active): Branch
    {
        return Branch::create([
            'name' => $name,
            'code' => $code,
            'address' => $name.' Address',
            'phone' => '9000000000',
            'email' => strtolower($code).'@example.test',
            'is_main' => $code === 'MAIN',
            'is_active' => $active,
        ]);
    }

    private function makeStaff(Branch $branch, array $permissions): User
    {
        return User::create([
            'branch_id' => $branch->id,
            'name' => 'Campus Staff',
            'email' => 'staff.'.$branch->id.'.'.implode('-', $permissions).'@example.test',
            'password' => 'password',
            'is_admin' => false,
            'role' => 'office_manager',
            'permissions' => $permissions,
        ]);
    }

    private function makeStudent(Branch $branch, string $name, string $admissionNo): Student
    {
        $user = User::create([
            'branch_id' => $branch->id,
            'name' => $name,
            'email' => strtolower(str_replace([' ', '-'], '.', $admissionNo)).'@example.test',
            'password' => 'password',
            'is_admin' => false,
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'admission_no' => $admissionNo,
            'class_name' => 'Class 1',
            'section' => 'A',
            'roll_no' => '1',
            'is_active' => true,
        ]);

        $student->setRelation('user', $user);

        return $student;
    }
}
