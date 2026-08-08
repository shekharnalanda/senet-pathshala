<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('school:about', function () {
    $this->info('Senet Pathshala management system');
})->purpose('Show application information');
