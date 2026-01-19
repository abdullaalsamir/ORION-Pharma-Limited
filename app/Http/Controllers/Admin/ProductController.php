<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Generic;
use App\Models\Product;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

class ProductController extends Controller
{
    public function index()
    {
        $menu = Menu::where('slug', 'products')->firstOrFail();
        $generics = Generic::orderBy('name')->get();
        return view('admin.products.index', compact('menu', 'generics'));
    }

    public function fetchProducts(Generic $generic)
    {
        $products = $generic->products()->orderBy('trade_name')->get();
        return response()->json([
            'html' => view('admin.products.partials.product-list', compact('products', 'generic'))->render()
        ]);
    }

    public function storeGeneric(Request $request)
    {
        $request->validate(['name' => 'required|unique:generics,name']);
        Generic::create(['name' => $request->name, 'is_active' => 1]);
        return back()->with('success', 'Generic added');
    }

    public function updateGeneric(Request $request, Generic $generic)
    {
        $request->validate(['name' => 'required|unique:generics,name,' . $generic->id]);

        $generic->update([
            'name' => $request->name,
            'is_active' => $request->boolean('is_active'),
        ]);

        return response()->json(['success' => true]);
    }

    public function storeProduct(Request $request, Generic $generic)
    {
        $request->validate([
            'trade_name' => 'required',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120'
        ]);

        $path = $this->handleImage($request->file('image'), $request->trade_name);
        $data = $request->except(['image']);
        $data['image_path'] = $path;
        $data['generic_id'] = $generic->id;
        $data['is_active'] = 1;

        Product::create($data);
        return response()->json(['success' => true]);
    }

    public function updateProduct(Request $request, Product $product)
    {
        $data = $request->except(['image', '_method', 'is_active']);

        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        if ($request->hasFile('image')) {
            if (Storage::disk('public')->exists($product->image_path)) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $this->handleImage($request->file('image'), $request->trade_name);
        }

        $product->update($data);
        return response()->json(['success' => true]);
    }

    public function deleteProduct(Product $product)
    {
        Storage::disk('public')->delete($product->image_path);
        $product->delete();
        return response()->json(['success' => true]);
    }

    private function handleImage($file, $name)
    {
        $fileName = \Illuminate\Support\Str::slug($name) . '-' . time() . '.webp';
        $path = "products/{$fileName}";
        if (!Storage::disk('public')->exists('products'))
            Storage::disk('public')->makeDirectory('products');

        $this->processImage($file->getRealPath(), storage_path("app/public/{$path}"), 16 / 9);
        return $path;
    }

    private function processImage($source, $dest, $ratio)
    {
        ini_set('memory_limit', '1024M');
        $info = getimagesize($source);
        $src = match ($info[2]) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($source),
            IMAGETYPE_PNG => imagecreatefrompng($source),
            IMAGETYPE_WEBP => imagecreatefromwebp($source),
            default => throw new Exception('Invalid Type')
        };

        imagepalettetotruecolor($src);
        imagealphablending($src, true);
        imagesavealpha($src, true);

        $w = $info[0];
        $h = $info[1];
        if (($w / $h) > $ratio) {
            $cw = $h * $ratio;
            $ch = $h;
            $sx = ($w - $cw) / 2;
            $sy = 0;
        } else {
            $cw = $w;
            $ch = $w / $ratio;
            $sx = 0;
            $sy = ($h - $ch) / 2;
        }

        $dst = imagecreatetruecolor(800, 450);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, $sx, $sy, 800, 450, $cw, $ch);
        imagewebp($dst, $dest, 80);
    }
}