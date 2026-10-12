<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('landing');

Route::view('/dashboard', 'dashboard')->name('dashboard');
