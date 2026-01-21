<?php

use Illuminate\Support\Facades\Route;

Route::view('/app/{any?}', 'app')->where('any', '.*');
