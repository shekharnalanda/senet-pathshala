<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('mci_fee_invoices', function (Blueprint $t): void {
            $t->id(); $t->unsignedBigInteger('branch_id')->index();
            $t->string('principal_type', 20); $t->string('principal_id', 100);
            $t->foreignId('linked_student_id')->nullable()->constrained('students')->nullOnDelete();
            $t->string('purpose', 150); $t->string('billing_month', 7);
            $t->unsignedBigInteger('amount_paise'); $t->char('billing_key', 64)->unique();
            $t->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $t->foreignId('fee_payment_id')->nullable()->constrained('fee_payments')->restrictOnDelete();
            $t->timestamps();
        });
        Schema::table('fee_payments', function (Blueprint $t): void {
            $t->uuid('mci_pay_order_id')->nullable()->unique();
            $t->string('transaction_ref', 22)->nullable()->unique();
            $t->string('fee_month', 7)->nullable();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('mci_fee_invoices');
        Schema::table('fee_payments', fn (Blueprint $t) => $t->dropColumn(['mci_pay_order_id', 'transaction_ref', 'fee_month']));
    }
};
