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

        $parentId = $request->parent_id;
        $isActive = $request->has('is_active');

        if ($parentId) {
            $parent = Menu::find($parentId);
            if ($parent && !$parent->is_active) {
                $isActive = false;
            }
        }

        $menu->update([
            'name' => $request->name,
            'parent_id' => $parentId,
            'is_active' => $isActive,
        ]);

        return back()->with('success', 'Menu updated successfully');
    }

    public function destroy(Menu $menu)
    {
        $oldOrder = $menu->order;
        $parentId = $menu->parent_id;

        $children = $menu->children()->orderBy('order')->get();
        $childCount = $children->count();

        if ($childCount > 0) {
            Menu::where('parent_id', $parentId)
                ->where('order', '>', $oldOrder)
                ->increment('order', $childCount);

            foreach ($children as $index => $child) {
                $child->update([
                    'parent_id' => $parentId,
                    'order' => $oldOrder + $index
                ]);
            }
        }

        $menu->delete();

        return back()->with('success', 'Menu deleted and hierarchy reorganized.');
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'menus' => 'required|array',
            'menus.*.id' => 'required|exists:menus,id',
            'menus.*.parent_id' => 'nullable|exists:menus,id',
            'menus.*.sort_order' => 'required|integer|min:0'
        ]);

        Menu::unguard();

        foreach ($request->menus as $item) {
            Menu::where('id', $item['id'])->update([
                'parent_id' => $item['parent_id'] ?? null,
                'order' => $item['sort_order']
            ]);
        }

        Menu::reguard();

        return response()->json(['success' => true]);
    }
}