<?php

namespace App\Http\Controllers;

use App\Models\Menu;

class PageController extends Controller
{
    public function home()
    {
        $menu = Menu::where('slug', 'home')->first();
        $sliders = \App\Models\Slider::where('is_active', 1)->orderBy('order')->get();
        return view('layouts.app', compact('menu', 'sliders'));
    }

    public function page(string $slug)
    {
        if ($slug === 'home')
            return redirect('/', 301);

        $menu = Menu::all()->first(function ($menu) use ($slug) {
            return $menu->full_slug === $slug;
        });

        abort_if(!$menu, 404);
        abort_if(!$menu->isEffectivelyActive(), 404);

        abort_if($menu->children()->exists(), 404);

        return view('layouts.app', compact('menu'));
    }
}