<?php

namespace Tests\Feature;

use App\Models\AdmissionApplication;
use App\Models\Branch;
use App\Models\ExamResult;
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
        $this->makeClass($branch);

        $this->get(route('admission', ['branch_id' => $branch->id]))
            ->assertOk()
            ->assertSee('ONLINE ADMISSION')
            ->assertSee('Main Campus')
            ->assertSee('Class 1');
    }

    public function test_admin_can_open_admission_application_detail_page(): void
    {
        $admin = $this->makeAdmin();
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

    public function test_id_card_list_respects_selected_campus(): void
    {
        $admin = $this->makeAdmin();
        $main = $this->makeBranch('Main Campus', 'MAIN', true);
        $second = $this->makeBranch('Second Campus', 'SECOND', true);
        $ownStudent = $this->makeStudent($main, 'Main ID Student', 'ID-MAIN-001');
        $otherStudent = $this->makeStudent($second, 'Second ID Student', 'ID-SECOND-001');

        $this->actingAs($admin)
            ->get(route('admin.student-id-cards.index', ['branch_id' => $main->id]))
            ->assertOk()
            ->assertSee($ownStudent->user->name)
            ->assertDontSee($otherStudent->user->name);
    }

    public function test_id_card_print_rejects_student_from_inactive_campus(): void
    {
        $admin = $this->makeAdmin();
        $active = $this->makeBranch('Main Campus', 'MAIN', true);
        $inactive = $this->makeBranch('Inactive Campus', 'OLD', false);
        $activeStudent = $this->makeStudent($active, 'Active ID Student', 'ID-ACTIVE-001');
        $inactiveStudent = $this->makeStudent($inactive, 'Inactive ID Student', 'ID-INACTIVE-001');

        $this->actingAs($admin)
            ->post(route('admin.student-id-cards.print'), [
                'students' => [$activeStudent->id, $inactiveStudent->id],
            ])
            ->assertStatus(422);
    }

    public function test_homework_staff_without_active_campus_is_rejected(): void
    {
        $staff = User::create([
            'name' => 'Unassigned Homework Staff',
            'email' => 'homework.unassigned@example.test',
            'password' => 'password',
            'is_admin' => false,
            'role' => 'office_manager',
            'permissions' => ['homework'],
        ]);

        $this->actingAs($staff)
            ->get(route('admin.homework.index'))
            ->assertForbidden();
    }

    public function test_report_card_staff_cannot_open_other_campus_student(): void
    {
        $main = $this->makeBranch('Main Campus', 'MAIN', true);
        $second = $this->makeBranch('Second Campus', 'SECOND', true);
        $staff = $this->makeStaff($main, ['report_cards']);
        $otherStudent = $this->makeStudent($second, 'Other Result Student', 'OTHER-RESULT-001');

        ExamResult::create([
            'student_id' => $otherStudent->id,
            'exam_name' => 'Annual Exam',
            'subject' => 'English',
            'max_marks' => 100,
            'marks_obtained' => 80,
            'exam_date' => '2026-08-23',
        ]);

        $this->actingAs($staff)
            ->get(route('admin.results.report-card', [$otherStudent, 'Annual Exam']))
            ->assertForbidden();
    }

    public function test_certificate_staff_cannot_generate_for_other_campus_student(): void
    {
        $main = $this->makeBranch('Main Campus', 'MAIN', true);
        $second = $this->makeBranch('Second Campus', 'SECOND', true);
        $staff = $this->makeStaff($main, ['certificates']);
        $otherStudent = $this->makeStudent($second, 'Other Certificate Student', 'OTHER-CERT-001');

        $this->actingAs($staff)
            ->post(route('admin.documents.generate'), [
                'student_id' => $otherStudent->id,
                'certificate_type' => 'bonafide',
                'purpose' => 'Testing',
            ])
            ->assertForbidden();
    }

    public function test_admin_dashboard_excludes_students_from_inactive_campuses(): void
    {
        $admin = $this->makeAdmin();
        $active = $this->makeBranch('Main Campus', 'MAIN', true);
        $inactive = $this->makeBranch('Old Campus', 'OLD2', false);
        $this->makeStudent($active, 'Active Dashboard Student', 'DASH-ACTIVE-001');
        $this->makeStudent($inactive, 'Inactive Dashboard Student', 'DASH-OLD-001');

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('studentCount', 1)
            ->assertViewHas('activeStudentCount', 1);
    }

    public function test_staff_dashboard_rejects_inactive_assigned_campus(): void
    {
        $inactive = $this->makeBranch('Inactive Staff Campus', 'STAFFOLD', false);
        $staff = $this->makeStaff($inactive, ['students']);

        $this->actingAs($staff)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    private function makeAdmin(): User
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'admin.'.uniqid().'@example.test',
            'password' => 'password',
            'is_admin' => true,
        ]);
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

    private function makeClass(Branch $branch): SchoolClass
    {
        return SchoolClass::create([
            'branch_id' => $branch->id,
            'name' => 'Class 1',
            'sections' => 'A,B',
            'sort_order' => 1,
            'is_active' => true,
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
