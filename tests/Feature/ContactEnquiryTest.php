<?php

namespace Tests\Feature;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactEnquiryTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_enquiry_can_be_submitted(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Test Parent',
            'mobile' => '9876543210',
            'email' => 'parent@example.com',
            'message' => 'Admission enquiry',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('contacts', [
            'name' => 'Test Parent',
            'mobile' => '9876543210',
        ]);
        $this->assertInstanceOf(Contact::class, Contact::first());
    }
}
