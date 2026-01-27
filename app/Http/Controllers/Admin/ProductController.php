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
        $validator = \Validator::make($request->all(), [
            'name' => 'required|unique:generics,name,' . $generic->id
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first()
            ], 422);
        }

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
        $request->validate([
            'trade_name' => 'required',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:51200'
        ]);

        $exists = Product::where('generic_id', $generic->id)
            ->where('trade_name', $request->trade_name)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'error' => "The product '{$request->trade_name}' already exists under '{$generic->name}'."
            ], 422);
        }

        try {
            $file = $request->file('image');
            $fileName = \Str::slug($request->trade_name) . '-' . time() . '.webp';
            $relativeDir = "products/{$generic->slug}";
            $path = "{$relativeDir}/{$fileName}";

            if (!\Storage::disk('public')->exists($relativeDir)) {
                \Storage::disk('public')->makeDirectory($relativeDir);
            }

            $this->processProductImage($file->getRealPath(), storage_path("app/public/{$path}"));

            Product::create([
                'trade_name' => $request->trade_name,
                'image_path' => $path,
                'generic_id' => $generic->id,
                'is_active' => 1,
            ] + $request->except(['image', '_token']));

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function updateProduct(Request $request, Product $product)
    {
        $request->validate([
            'trade_name' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:51200'
        ]);

        $exists = Product::where('generic_id', $product->generic_id)
            ->where('trade_name', $request->trade_name)
            ->where('id', '!=', $product->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'error' => "Another product named '{$request->trade_name}' already exists in this category."
            ], 422);
        }

        $data = $request->except(['image', '_method', 'is_active']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        if ($request->hasFile('image')) {
            \Storage::disk('public')->delete($product->image_path);
            $fullPath = storage_path("app/public/{$product->image_path}");
            $this->processProductImage($request->file('image')->getRealPath(), $fullPath);
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

    private function processProductImage($sourcePath, $destinationPath)
    {
        ini_set('memory_limit', '1024M');

        if (!extension_loaded('gd')) {
            throw new Exception('GD library is not installed or enabled.');
        }

        $info = getimagesize($sourcePath);
        if (!$info) {
            throw new Exception('Invalid image file.');
        }

        $width = $info[0];
        $height = $info[1];
        $type = $info[2];

        switch ($type) {
            case IMAGETYPE_JPEG:
                $src = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $src = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_WEBP:
                $src = imagecreatefromwebp($sourcePath);
                break;
            default:
                throw new Exception('Unsupported image type.');
        }

        if (!$src) {
            throw new Exception('Failed to load image resource.');
        }

        imagepalettetotruecolor($src);
        imagealphablending($src, true);
        imagesavealpha($src, true);

        $targetRatio = 16 / 9;
        $currentRatio = $width / $height;

        if ($currentRatio > $targetRatio) {
            $cropWidth = $height * $targetRatio;
            $cropHeight = $height;
            $srcX = ($width - $cropWidth) / 2;
            $srcY = 0;
        } else {
            $cropWidth = $width;
            $cropHeight = $width / $targetRatio;
            $srcX = 0;
            $srcY = ($height - $cropHeight) / 2;
        }

        $finalWidth = $cropWidth;
        if ($finalWidth > 2000) {
            $finalWidth = 2000;
        }
        $finalHeight = $finalWidth / $targetRatio;

        $dst = imagecreatetruecolor($finalWidth, $finalHeight);

        imagealphablending($dst, false);
        imagesavealpha($dst, true);

        imagecopyresampled($dst, $src, 0, 0, $srcX, $srcY, $finalWidth, $finalHeight, $cropWidth, $cropHeight);

        if (!imagewebp($dst, $destinationPath, 70)) {
            throw new Exception('Failed to save WebP image.');
        }

        imagedestroy($src);
        imagedestroy($dst);
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