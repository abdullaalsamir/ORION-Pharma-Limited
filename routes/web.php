<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\MenuController;

Route::get('/', [PageController::class, 'home'])->name('home');

Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');

Route::post('/admin/logout', [AuthController::class, 'logout'])
    ->middleware('auth:admin')
    ->name('admin.logout');

Route::get('/admin', [AuthController::class, 'dashboard'])->middleware('auth:admin')->name('admin.dashboard');

Route::prefix('admin')->middleware('auth:admin')->group(function () {

    Route::get('/pages', [MenuController::class, 'pages'])->name('admin.pages');

    Route::get('/products', function () {
        return view('admin.products');
    })->name('admin.products');

    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('admin.settings');

});

Route::prefix('admin')->middleware('auth:admin')->group(function () {

    Route::get('/menus', [MenuController::class, 'index'])->name('admin.menus');
    Route::post('/menus', [MenuController::class, 'store'])->name('admin.menus.store');
    Route::put('/menus/{menu}', [MenuController::class, 'update'])->name('admin.menus.update');
    Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])->name('admin.menus.delete');

});

Route::post('/admin/menus/update-order', [MenuController::class, 'updateOrder'])->name('admin.menus.update-order');

Route::put('/admin/pages/{menu}', [MenuController::class, 'updatePage'])
    ->middleware('auth:admin')
    ->name('admin.pages.update');

Route::get('/admin/pages', [MenuController::class, 'pages'])
    ->middleware('auth:admin')
    ->name('admin.pages');

Route::get('{slug}', [PageController::class, 'page'])
    ->where('slug', '.*');
