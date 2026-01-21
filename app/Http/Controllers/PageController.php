<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CsrController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\ScholarshipController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\BoardDirectorController;
use App\Http\Controllers\Admin\MedicalJournalController;
use App\Http\Controllers\Admin\PriceSensitiveInformationController;

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
            if ($menu->is_multifunctional && $menu->slug === 'news-and-announcements') {
                return (new NewsController)->frontendIndex($menu);
            }
            if ($menu->slug === 'board-of-directors') {
                return (new BoardDirectorController)->frontendIndex($menu);
            }
            if ($menu->slug === 'scholarship') {
                return (new ScholarshipController)->frontendIndex($menu);
            }
            if ($menu->slug === 'products') {
                return (new ProductController)->frontendIndex($menu);
            }
            if ($menu->slug === 'medical-journals') {
                return (new MedicalJournalController)->frontendIndex($menu);
            }
            if ($menu->slug === 'price-sensitive-information') {
                return (new PriceSensitiveInformationController)->frontendIndex($menu);
            }

            abort_if($menu->children()->exists(), 404);
            return view('layouts.app', compact('menu'));
        }

        $segments = explode('/', $slug);
        $itemSlug = array_pop($segments);
        $parentPath = implode('/', $segments);

        $parentMenu = Menu::all()->first(fn($m) => $m->full_slug === $parentPath);

        if ($parentMenu && $parentMenu->is_multifunctional) {

            if ($parentMenu->slug === 'price-sensitive-information' && str_ends_with($itemSlug, '.pdf')) {
                return (new PriceSensitiveInformationController)->servePdf($parentPath, $itemSlug);
            }

            if ($parentMenu->slug === 'csr-list') {
                return (new CsrController)->frontendShow($parentMenu, $itemSlug);
            }

            if ($parentMenu->slug === 'news-and-announcements') {
                return (new NewsController)->frontendShow($parentMenu, $itemSlug);
            }

            if ($parentMenu->slug === 'board-of-directors') {
                return (new BoardDirectorController)->frontendShow($parentMenu, $itemSlug);
            }
        }

        if (!$parentMenu) {
            $baseMenu = Menu::where('slug', 'products')->first();
            if ($baseMenu && count($segments) > 0 && $segments[0] === $baseMenu->slug) {
                $genericSlug = $segments[1] ?? null;
                if ($genericSlug) {
                    return (new ProductController)->frontendShow($genericSlug, $itemSlug, $baseMenu);
                }
            }
        }

        abort(404);
    }

    public function image($path, $filename)
    {
        if (str_starts_with($path, 'products/')) {
            $genericSlug = str_replace('products/', '', $path);
            return (new ProductController)->serveProductImage($genericSlug, $filename);
        }

        $menu = Menu::all()->first(fn($m) => $m->full_slug === $path);
        if (!$menu)
            abort(404);

        if ($menu->is_multifunctional) {
            if ($menu->slug === 'csr-list') {
                return (new CsrController)->serveCsrImage($filename);
            }
            if ($menu->slug === 'news-and-announcements') {
                return (new NewsController)->serveNewsImage($filename);
            }
            if ($menu->slug === 'board-of-directors') {
                return (new BoardDirectorController)->serveImage($filename);
            }
            if ($menu->slug === 'scholarship') {
                return (new ScholarshipController)->serveScholarImage($filename);
            }
        }

        return (new BannerController)->serveBannerImage($menu, $filename);
    }
}