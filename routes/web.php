<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/login', function () {
    // Replace 'login' with the path to your Vue files
    return Inertia::render('/login/Login');
});