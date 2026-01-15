<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\SliderController;

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

});

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth:admin', 'no.cache'])
    ->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/menus', [MenuController::class, 'index'])->name('menus');
        Route::post('/menus', [MenuController::class, 'store'])->name('menus.store');
        Route::put('/menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
        Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])->name('menus.delete');
        Route::post('/menus/update-order', [MenuController::class, 'updateOrder'])->name('menus.update-order');

        Route::get('/pages', [MenuController::class, 'pages'])->name('pages');
        Route::put('/pages/{menu}', [MenuController::class, 'updatePage'])->name('pages.update');

        Route::get('/banners', [BannerController::class, 'index'])->name('banners');
        Route::get('/banners/fetch/{menu}', [BannerController::class, 'getBanners'])->name('banners.fetch');
        Route::get('/banners/get-for-editor/{menu}', [BannerController::class, 'getBannersForEditor']);
        Route::post('/banners/upload/{menu}', [BannerController::class, 'store'])->name('banners.upload');
        Route::match(['post', 'put'], '/banners/{banner}', [BannerController::class, 'update'])->name('banners.update');
        Route::delete('/banners/{banner}', [BannerController::class, 'destroy'])->name('banners.delete');

        Route::get('/sliders', [SliderController::class, 'index'])->name('sliders.index');
        Route::post('/sliders', [SliderController::class, 'store'])->name('sliders.store');
        Route::put('/sliders/{slider}', [SliderController::class, 'update'])->name('sliders.update');
        Route::delete('/sliders/{slider}', [SliderController::class, 'destroy'])->name('sliders.delete');
        Route::post('/sliders/update-order', [SliderController::class, 'updateOrder'])->name('sliders.update-order');

        Route::get('/settings', function () {
            return view('admin.settings.index');
        })->name('settings');

        Route::get('/{slug}', [MenuController::class, 'showMultifunctional'])->name('multifunctional');

    });

Route::get('/', [PageController::class, 'home'])->name('home');

Route::get('sliders/{filename}', [SliderController::class, 'serveSliderImage'])
    ->where('filename', '^[0-9]+\.webp$');

Route::get('{path}/{filename}', [BannerController::class, 'serveBannerImage'])
    ->where('path', '.*')
    ->where('filename', '^[0-9]+\.webp$');

Route::get('{slug}', [PageController::class, 'page'])
    ->where('slug', '.*');
