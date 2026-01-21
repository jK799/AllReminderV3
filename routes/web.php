<?php

use Illuminate\Support\Facades\Route;

// SPA entry
Route::view('/', 'app')->name('spa');

Route::view('/{any}', 'app')
    ->where('any', '.*');
