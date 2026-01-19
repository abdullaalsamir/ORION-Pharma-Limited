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
        $validator = \Validator::make($request->all(), [
            'name' => 'required|unique:generics,name'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()->first()], 422);
        }

        Generic::create(['name' => $request->name, 'is_active' => 1]);
        return response()->json(['success' => true]);
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

    public function deleteGeneric(Generic $generic)
    {
        try {
            $directory = "products/{$generic->slug}";
            if (Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->deleteDirectory($directory);
            }

            $generic->delete();

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function storeProduct(Request $request, Generic $generic)
    {
        $request->validate(['trade_name' => 'required', 'image' => 'required|image']);

        $path = $this->handleImage($request->file('image'), $request->trade_name, $generic->slug);

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
            $data['image_path'] = $this->handleImage($request->file('image'), $request->trade_name, $product->generic->slug);
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

    private function handleImage($file, $name, $genericSlug)
    {
        $fileName = \Illuminate\Support\Str::slug($name) . '.webp';
        $relativeDir = "products/{$genericSlug}";
        $path = "{$relativeDir}/{$fileName}";

        if (!Storage::disk('public')->exists($relativeDir)) {
            Storage::disk('public')->makeDirectory($relativeDir);
        }

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

    public function frontendIndex($menu)
    {
        $products = Product::where('is_active', 1)
            ->whereHas('generic', fn($q) => $q->where('is_active', 1))
            ->with('generic')
            ->get();

        return view('products.index', compact('products', 'menu'));
    }

    public function frontendShow($generic_slug, $product_slug, $menu = null)
    {
        if (!$menu) {
            $menu = Menu::where('slug', 'products')->first();
        }

        $product = Product::whereHas('generic', fn($q) => $q->where('slug', $generic_slug))
            ->where(\DB::raw('LOWER(REPLACE(trade_name, " ", "-"))'), $product_slug)
            ->firstOrFail();

        return view('products.show', compact('product', 'menu'));
    }

    public function serveProductImage($generic_slug, $filename)
    {
        $path = storage_path("app/public/products/{$generic_slug}/{$filename}");
        abort_if(!file_exists($path), 404);
        return response()->file($path);
    }
}