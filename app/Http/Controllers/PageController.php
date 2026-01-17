<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CsrController;

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

        $menu = Menu::all()->first(fn($m) => $m->full_slug === $slug);

        if ($menu) {
            abort_if(!$menu->isEffectivelyActive(), 404);

            if ($menu->is_multifunctional && $menu->slug === 'csr-list') {
                return (new CsrController)->frontendIndex($menu);
            }

            abort_if($menu->children()->exists(), 404);
            return view('layouts.app', compact('menu'));
        }

        $segments = explode('/', $slug);
        $itemSlug = array_pop($segments);
        $parentPath = implode('/', $segments);

        $parentMenu = Menu::all()->first(fn($m) => $m->full_slug === $parentPath);
        if ($parentMenu && $parentMenu->is_multifunctional && $parentMenu->slug === 'csr-list') {
            return (new CsrController)->frontendShow($parentMenu, $itemSlug);
        }

        abort(404);
    }

    public function image($path, $filename)
    {
        $menu = Menu::all()->first(fn($m) => $m->full_slug === $path);
        if (!$menu)
            abort(404);

        if ($menu->is_multifunctional && $menu->slug === 'csr-list') {
            return (new CsrController)->serveCsrImage($filename);
        }

        return (new BannerController)->serveBannerImage($menu, $filename);
    }
}