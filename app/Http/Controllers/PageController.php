<?php

namespace App\Http\Controllers;

use App\Models\Menu;

class PageController extends Controller
{
    public function home()
    {
        return view('pages.home');
    }

    public function about()
    {
        return view('pages.about');
    }

    public function products()
    {
        return view('pages.products');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function page(string $slug)
    {
        $menu = Menu::all()->first(function ($menu) use ($slug) {
            return $menu->full_slug === $slug;
        });

        abort_if(!$menu, 404);

        abort_if(!$menu->isEffectivelyActive(), 404);

        abort_if($menu->children()->exists(), 404);

        return view('pages.dynamic', compact('menu'));
    }
}
