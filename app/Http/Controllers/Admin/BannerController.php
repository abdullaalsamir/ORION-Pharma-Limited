<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

class BannerController extends Controller
{
    public function index()
    {
        $leafMenus = Menu::getFunctionalLeafMenus();
        return view('admin.banners.index', compact('leafMenus'));
    }

    public function getBanners(Menu $menu)
    {
        $banners = $menu->banners()->orderBy('created_at', 'asc')->get();

        return response()->json([
            'html' => view('admin.banners.partials.banner-list', compact('banners', 'menu'))->render()
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
            $relativeDir = "banners/{$menu->slug}";

            if (!Storage::disk('public')->exists($relativeDir)) {
                Storage::disk('public')->makeDirectory($relativeDir);
            }

            $fullPath = storage_path("app/public/{$relativeDir}/{$fileName}");

            $this->processBanner($file->getRealPath(), $fullPath);

            Banner::create([
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

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:51200',
            'is_active' => 'required'
        ]);

        try {
            $banner->is_active = $request->is_active;

            if ($request->hasFile('image')) {
                $fullPath = storage_path("app/public/{$banner->file_path}");
                $this->processBanner($request->file('image')->getRealPath(), $fullPath);
            }

            $banner->save();
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function processBanner($sourcePath, $destinationPath)
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
            throw new Exception('Failed to save WebP image.');
        }

        imagedestroy($src);
        imagedestroy($dst);
    }

    public function destroy(Banner $banner)
    {
        Storage::disk('public')->delete($banner->file_path);
        $banner->delete();
        return response()->json(['success' => true]);
    }

    public function getBannersForEditor(Menu $menu)
    {
        $banners = $menu->banners()->where('is_active', 1)->orderBy('created_at', 'asc')->get();
        $fullSlug = $menu->full_slug;

        return response()->json($banners->map(function ($banner) use ($fullSlug) {
            return [
                'url' => '/' . ltrim($fullSlug, '/') . '/' . $banner->file_name,
                'name' => $banner->file_name
            ];
        }));
    }

    public function serveBannerImage($menu, $filename)
    {
        $banner = $menu->banners()->where('file_name', $filename)->first();
        abort_if(!$banner, 404);

        $storagePath = storage_path('app/public/' . $banner->file_path);
        abort_if(!file_exists($storagePath), 404);
        return response()->file($storagePath);
    }
}