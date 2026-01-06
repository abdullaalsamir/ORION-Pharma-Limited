<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::whereNull('parent_id')
            ->orderBy('order')
            ->with('children.children')
            ->get();

        return view('admin.menus.index', compact('menus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'parent_id' => 'nullable|exists:menus,id'
        ]);

        Menu::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'order' => Menu::where('parent_id', $request->parent_id)->max('order') + 1
        ]);

        return back()->with('success', 'Menu created successfully');
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name' => 'required|string',
            'parent_id' => 'nullable|exists:menus,id',
        ]);

        $menu->update([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'Menu updated successfully');
    }


    public function destroy(Menu $menu)
    {
        $menu->delete(); // children auto-unlinked via nullOnDelete
        return back()->with('success', 'Menu deleted');
    }
}
