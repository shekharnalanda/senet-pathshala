<?php
use App\Http\Controllers\MciPayController;
use Illuminate\Support\Facades\Route;
Route::get('/mci-pay',[MciPayController::class,'index'])->name('mci-pay.index');
Route::post('/mci-pay/orders',[MciPayController::class,'store'])->middleware('throttle:12,1')->name('mci-pay.store');
Route::get('/mci-pay/orders/{order}',[MciPayController::class,'show'])->whereUuid('order')->name('mci-pay.show');
Route::post('/mci-pay/orders/{order}/refresh',[MciPayController::class,'refresh'])->whereUuid('order')->middleware('throttle:20,1')->name('mci-pay.refresh');
Route::post('/mci-pay/callback',[MciPayController::class,'callback'])->middleware('throttle:180,1');
Route::get('/admin/upi-payments',[MciPayController::class,'admin'])->middleware('auth')->name('mci-pay.admin');


Route::middleware('auth')->group(function () {
    Route::get('/admin/upi-invoices',[\App\Http\Controllers\Admin\MciFeeInvoiceController::class,'index'])->name('mci-pay.invoices');
    Route::post('/admin/upi-invoices',[\App\Http\Controllers\Admin\MciFeeInvoiceController::class,'store'])->middleware('throttle:30,1')->name('mci-pay.invoices.store');
    Route::post('/admin/upi-invoices/{invoice}/link',[\App\Http\Controllers\Admin\MciFeeInvoiceController::class,'link'])->middleware('throttle:20,1')->name('mci-pay.invoices.link');
});

Route::get('/mci-pay/access',[MciPayController::class,'access'])->name('mci-pay.access');
Route::post('/mci-pay/access',[MciPayController::class,'authenticate'])->middleware('throttle:5,1')->name('mci-pay.authenticate');
