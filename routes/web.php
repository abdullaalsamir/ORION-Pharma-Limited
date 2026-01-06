<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.home');
});

Route::get('/about', function () {
    return view('pages.about');
});

Route::get('/products', function () {
    return view('pages.products');
});

Route::get('/contact', function () {
    return view('pages.contact');
});
