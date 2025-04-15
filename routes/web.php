<?php

use Illuminate\Support\Facades\Route;
use Dfoxx\Shibboleth\ShibbolethController;

Route::get('shibboleth', ShibbolethController::class)->name('shibboleth');
