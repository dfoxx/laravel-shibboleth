<?php

use Illuminate\Support\Facades\Route;
use Dfoxx\Shibboleth\ShibbolethController;

Route::get('shibboleth', [ShibbolethController::class, 'shibboleth'])->name('shibboleth');
Route::get('login', [ShibbolethController::class, 'login'])->name('login');
Route::get('logout', [ShibbolethController::class, 'logout'])->name('logout');
