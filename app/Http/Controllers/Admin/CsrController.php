<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CsrItem;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

class CsrController extends Controller
{
    public function index()
    {
        $menu = Menu::where('slug', 'csr-list')->firstOrFail();

        $groupedCsr = CsrItem::orderBy('csr_date', 'desc')
            ->orderBy('order', 'asc')
            ->get()
            ->groupBy(function ($item) {
                return $item->csr_date->format('Y-m-d');
            });

        return view('admin.csr-list.index', compact('menu', 'groupedCsr'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'csr_date' => 'required|date',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:51200',
        ]);

        try {
            $file = $request->file('image');
            $fileName = time() . '.webp';
            $path = "csr/{$fileName}";

            if (!Storage::disk('public')->exists('csr')) {
                Storage::disk('public')->makeDirectory('csr');
            }

            $this->processCsrImage($file->getRealPath(), storage_path("app/public/{$path}"));

            CsrItem::create([
                'title' => $request->title,
                'description' => $request->description,
                'csr_date' => $request->csr_date,
                'image_path' => $path,
                'is_active' => 1,
                'order' => CsrItem::where('csr_date', $request->csr_date)->max('order') + 1
            ]);

            return back()->with('success', 'CSR added successfully');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, CsrItem $csrItem)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'csr_date' => 'required|date',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:51200',
        ]);

        try {
            if ($request->hasFile('image')) {
                Storage::disk('public')->delete($csrItem->image_path);
                $this->processCsrImage($request->file('image')->getRealPath(), storage_path("app/public/{$csrItem->image_path}"));
            }

            $csrItem->update([
                'title' => $request->title,
                'description' => $request->description,
                'csr_date' => $request->csr_date,
                'is_active' => $request->is_active
            ]);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function processCsrImage($sourcePath, $destinationPath)
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

    public function updateOrder(Request $request)
    {
        foreach ($request->orders as $item) {
            CsrItem::where('id', $item['id'])->update(['order' => $item['order']]);
        }
        return response()->json(['success' => true]);
    }

    public function destroy(CsrItem $csrItem)
    {
        Storage::disk('public')->delete($csrItem->image_path);
        $csrItem->delete();
        return back()->with('success', 'CSR deleted successfully');
    }

    public function serveCsrImage($filename)
    {
        $storagePath = storage_path('app/public/csr/' . $filename);
        abort_if(!file_exists($storagePath), 404);
        return response()->file($storagePath);
    }

    public function frontendIndex($menu)
    {
        $items = CsrItem::where('is_active', 1)
            ->orderBy('csr_date', 'desc')
            ->orderBy('order', 'asc')
            ->paginate(9);

        return view('csr.index', compact('items', 'menu'));
    }

    public function frontendShow($menu, $slug)
    {
        $item = CsrItem::where('slug', $slug)->where('is_active', 1)->firstOrFail();

        $related = CsrItem::where('is_active', 1)
            ->where('id', '!=', $item->id)
            ->latest('csr_date')
            ->take(3)
            ->get();

        return view('csr.show', compact('item', 'related', 'menu'));
    }
}