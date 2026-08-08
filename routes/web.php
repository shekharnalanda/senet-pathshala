<?php

use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Admin\SchoolSettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () { return view('home'); })->name('home');
Route::view('/about', 'about')->name('about');
Route::view('/admission', 'admission')->name('admission');
Route::view('/gallery', 'gallery')->name('gallery');
Route::view('/contact', 'contact')->name('contact');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::view('/', 'admin.dashboard')->name('dashboard');
    Route::get('/notices', [NoticeController::class, 'index'])->name('notices.index');
    Route::post('/notices', [NoticeController::class, 'store'])->name('notices.store');
    Route::put('/notices/{notice}', [NoticeController::class, 'update'])->name('notices.update');
    Route::delete('/notices/{notice}', [NoticeController::class, 'destroy'])->name('notices.destroy');
    Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
    Route::post('/gallery', [GalleryController::class, 'store'])->name('gallery.store');
    Route::delete('/gallery/{gallery}', [GalleryController::class, 'destroy'])->name('gallery.destroy');
    Route::get('/settings', [SchoolSettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SchoolSettingController::class, 'update'])->name('settings.update');
});

Route::get('/login', function () { return response('Login module coming next.', 200); })->name('login');
