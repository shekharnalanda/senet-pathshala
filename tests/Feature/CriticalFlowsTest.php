<?php

namespace Tests\Feature;

use App\Models\AdmissionApplication;
use App\Models\Branch;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CriticalFlowsTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_admission_page_renders_with_active_campus_and_class(): void
    {
        $branch = Branch::create([
            'name' => 'Main Campus',
            'code' => 'MAIN',
            'address' => 'Test Address',
            'phone' => '9000000000',
            'email' => 'main@example.test',
            'is_main' => true,
            'is_active' => true,
        ]);

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

        $branch = Branch::create([
            'name' => 'Main Campus',
            'code' => 'MAIN',
            'is_main' => true,
            'is_active' => true,
        ]);

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
        Branch::create([
            'name' => 'Inactive Campus',
            'code' => 'OLD',
            'is_main' => false,
            'is_active' => false,
        ]);

        $this->get(route('admission'))
            ->assertOk()
            ->assertDontSee('Inactive Campus');
    }
}
