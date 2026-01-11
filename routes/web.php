<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuController;

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

});

Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth:admin')
    ->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/menus', [MenuController::class, 'index'])->name('menus');
        Route::post('/menus', [MenuController::class, 'store'])->name('menus.store');
        Route::put('/menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
        Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])->name('menus.delete');
        Route::post('/menus/update-order', [MenuController::class, 'updateOrder'])->name('menus.update-order');

        Route::get('/pages', [MenuController::class, 'pages'])->name('pages');
        Route::put('/pages/{menu}', [MenuController::class, 'updatePage'])->name('pages.update');

        Route::get('/settings', function () {
            return view('admin.settings.index');
        })->name('settings');

    });

Route::get('/', [PageController::class, 'home'])->name('home');

Route::get('{slug}', [PageController::class, 'page'])
    ->where('slug', '.*');
