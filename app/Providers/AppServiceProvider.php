<?php

namespace App\Providers;

use App\Models\SchoolSetting;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}
    public function boot(): void
    {
        Blade::if('admin', fn () => auth()->check() && auth()->user()->is_admin);
        View::composer(['layouts.header','layouts.footer'], function ($view) {
            $websiteSettings = null;
            try { if (Schema::hasTable('school_settings')) $websiteSettings = SchoolSetting::first(); } catch (\Throwable $e) {}
            $view->with('websiteSettings', $websiteSettings);
        });
    }
}
