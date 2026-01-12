<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

class ImageController extends Controller
{
    public function index()
    {
        $leafMenus = Menu::getFunctionalLeafMenus();
        return view('admin.images.index', compact('leafMenus'));
    }

    public function getImages(Menu $menu)
    {
        $images = $menu->images()->orderBy('created_at', 'asc')->get();

        return response()->json([
            'html' => view('admin.images.partials.image-list', compact('images', 'menu'))->render()
        ]);
    }

    public function store(Request $request, Menu $menu)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:51200'
        ]);

        try {
            $file = $request->file('image');
            $fileName = time() . '.webp';
            $relativeDir = "menu_images/{$menu->slug}";

            if (!Storage::disk('public')->exists($relativeDir)) {
                Storage::disk('public')->makeDirectory($relativeDir);
            }

            $fullPath = storage_path("app/public/{$relativeDir}/{$fileName}");

            $this->processImage($file->getRealPath(), $fullPath);

            MenuImage::create([
                'menu_id' => $menu->id,
                'file_name' => $fileName,
                'file_path' => "{$relativeDir}/{$fileName}",
                'is_active' => 1
            ]);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, MenuImage $image)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:51200',
            'is_active' => 'required'
        ]);

        try {
            $image->is_active = $request->is_active;

            if ($request->hasFile('image')) {
                $fullPath = storage_path("app/public/{$image->file_path}");
                $this->processImage($request->file('image')->getRealPath(), $fullPath);
            }

            $image->save();
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function processImage($sourcePath, $destinationPath)
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

        $targetRatio = 48 / 9;
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
            throw new Exception('Failed to save WebP image. Check if WebP support is enabled in GD.');
        }

        imagedestroy($src);
        imagedestroy($dst);
    }

    public function destroy(MenuImage $image)
    {
        Storage::disk('public')->delete($image->file_path);
        $image->delete();
        return response()->json(['success' => true]);
    }

    public function getImagesForEditor(Menu $menu)
    {
        $images = $menu->images()->where('is_active', 1)->orderBy('created_at', 'asc')->get();
        $fullSlug = $menu->full_slug;

        return response()->json($images->map(function ($img) use ($fullSlug) {
            return [
                'url' => '/' . ltrim($fullSlug, '/') . '/' . $img->file_name,
                'name' => $img->file_name
            ];
        }));
    }

    public function servePublicImage($path, $filename)
    {
        $menu = Menu::all()->first(function ($m) use ($path) {
            return $m->full_slug === $path;
        });

        if (!$menu) {
            $menu = Menu::where('slug', $path)->whereNull('parent_id')->first();
        }

        if (!$menu)
            abort(404);

        $image = $menu->images()->where('file_name', $filename)->first();

        if (!$image)
            abort(404);

        $storagePath = storage_path('app/public/' . $image->file_path);

        if (!file_exists($storagePath))
            abort(404);

        return response()->file($storagePath, [
            'Content-Type' => 'image/webp',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}