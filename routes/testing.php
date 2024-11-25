<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('punto-venta-seed', function() {
    Artisan::call('db:seed --class E2ESeeder');
});
