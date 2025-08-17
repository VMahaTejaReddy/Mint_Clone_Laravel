<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'register');

Route::view('/login', 'login');

Route::view('/dashboard', 'dashboard');