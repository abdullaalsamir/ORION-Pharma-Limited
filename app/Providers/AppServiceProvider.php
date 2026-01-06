<?php

namespace App\Providers;

use App\Models\Menu;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // PUBLIC FRONTEND MENUS (ACTIVE ONLY)
        View::composer([
            'layouts.*',
            'pages.*',
            'partials.*'
        ], function ($view) {

            $menus = Menu::whereNull('parent_id')
                ->where('is_active', true)
                ->orderBy('order')
                ->with([
                    'children' => function ($q) {
                        $q->where('is_active', true)
                            ->orderBy('order')
                            ->with([
                                'children' => function ($q) {
                                    $q->where('is_active', true)
                                        ->orderBy('order');
                                }
                            ]);
                    }
                ])->get();

            $view->with('menus', $menus);
        });
    }
}

