<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use App\Models\{Branch,User,Student,MciFeeInvoice,MciPayOrder,FeePayment};
use App\Services\MciPayClient;
class MciFeeLedgerTest extends TestCase
{
 use RefreshDatabase;
 protected function setUp():void {parent::setUp();config(['app.key'=>'base64:'.base64_encode(str_repeat('t',32)),'mci_pay.enabled'=>true]);}
 private function context():array { $b=Branch::create(['code'=>'TEST','name'=>'Test branch','is_active'=>true]);$u=User::factory()->create(['branch_id'=>$b->id,'is_admin'=>false]);$s=Student::create(['branch_id'=>$b->id,'user_id'=>$u->id,'admission_no'=>'ST-1','is_active'=>true]);return [$b,$u,$s]; }
 private function order(Student $s,array $metadata=[]):MciPayOrder {return MciPayOrder::create(['id'=>(string)Str::uuid(),'branch_id'=>$s->branch_id,'principal_type'=>'student','principal_id'=>(string)$s->id,'active_key'=>(string)Str::uuid(),'student_reference'=>$s->admission_no,'payer_name'=>'Test','purpose'=>'Monthly fee','amount_paise'=>50000,'status'=>'verified','reference'=>'123456789012','payment_date'=>today(),'central_receipt'=>'CENTRAL-1','metadata'=>$metadata]);}
 public function test_invoice_payment_adds_new_fee_once_and_student_sees_bill():void { [$b,$u,$s]=$this->context();$invoice=MciFeeInvoice::create(['branch_id'=>$b->id,'principal_type'=>'student','principal_id'=>$s->id,'purpose'=>'October tuition','billing_month'=>'2026-10','amount_paise'=>50000,'billing_key'=>str_repeat('a',64),'created_by'=>$u->id]);$o=$this->order($s,['invoice_id'=>$invoice->id]);$this->actingAs($u)->get('/mci-pay')->assertOk()->assertSee('October tuition');app(MciPayClient::class)->applyVerified($o);$this->assertSame('applied',$o->fresh()->status);app(MciPayClient::class)->applyVerified($o->fresh());$this->assertDatabaseCount('fee_payments',1);$this->assertDatabaseHas('fee_payments',['student_id'=>$s->id,'total_fee'=>500,'amount_paid'=>500,'transaction_ref'=>'123456789012']);$this->assertNotNull($invoice->fresh()->fee_payment_id);$u->update(['is_admin'=>true]);$this->get('/admin/upi-payments')->assertOk();$this->get('/admin/upi-invoices')->assertOk(); }
 public function test_existing_balance_payment_does_not_increase_total_fee():void { [$b,$u,$s]=$this->context();FeePayment::create(['student_id'=>$s->id,'total_fee'=>1000,'amount_paid'=>500,'payment_date'=>today(),'payment_mode'=>'Cash','receipt_no'=>'OLD-001']);$o=$this->order($s);app(MciPayClient::class)->applyVerified($o);$this->assertSame('applied',$o->fresh()->status);$this->assertEquals(1000,FeePayment::latest('id')->first()->total_fee);$this->assertEquals(1000,FeePayment::sum('amount_paid')); }
 public function test_other_student_cannot_view_order_or_invoice_choice():void { [$b,$u,$s]=$this->context();$o=$this->order($s);$o->update(['principal_id'=>'999']);$this->actingAs($u)->get(route('mci-pay.show',$o))->assertForbidden();$this->post('/mci-pay/orders',['choice'=>'invoice:999'])->assertSessionHasErrors('choice'); }
}
