<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsItem;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

class NewsController extends Controller
{
    public function index()
    {
        $menu = Menu::where('slug', 'news-and-announcements')->firstOrFail();

        $groupedNews = NewsItem::orderBy('news_date', 'desc')
            ->orderBy('order', 'asc')
            ->get()
            ->groupBy(function ($item) {
                return $item->news_date->format('Y-m-d');
            });

        return view('admin.news-and-announcements.index', compact('menu', 'groupedNews'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'news_date' => 'required|date',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:51200',
        ]);

        try {
            $file = $request->file('image');
            $fileName = time() . '.webp';
            $path = "news/{$fileName}";

            if (!Storage::disk('public')->exists('news')) {
                Storage::disk('public')->makeDirectory('news');
            }

            $this->processNewsImage($file->getRealPath(), storage_path("app/public/{$path}"));

            NewsItem::create([
                'title' => $request->title,
                'description' => $request->description,
                'news_date' => $request->news_date,
                'image_path' => $path,
                'is_active' => 1,
                'order' => NewsItem::where('news_date', $request->news_date)->max('order') + 1
            ]);

            return back()->with('success', 'News added successfully');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, NewsItem $newsItem)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'news_date' => 'required|date',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:51200',
        ]);

        try {
            if ($request->hasFile('image')) {
                Storage::disk('public')->delete($newsItem->image_path);
                $this->processNewsImage($request->file('image')->getRealPath(), storage_path("app/public/{$newsItem->image_path}"));
            }

            $newsItem->update([
                'title' => $request->title,
                'description' => $request->description,
                'news_date' => $request->news_date,
                'is_active' => $request->is_active
            ]);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function processNewsImage($sourcePath, $destinationPath)
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
            NewsItem::where('id', $item['id'])->update(['order' => $item['order']]);
        }
        return response()->json(['success' => true]);
    }

    public function destroy(NewsItem $newsItem)
    {
        Storage::disk('public')->delete($newsItem->image_path);
        $newsItem->delete();
        return back()->with('success', 'News deleted successfully');
    }

    public function serveNewsImage($filename)
    {
        $storagePath = storage_path('app/public/news/' . $filename);
        abort_if(!file_exists($storagePath), 404);
        return response()->file($storagePath);
    }

    public function frontendIndex($menu)
    {
        $items = NewsItem::where('is_active', 1)
            ->orderBy('news_date', 'desc')
            ->orderBy('order', 'asc')
            ->paginate(9);

        return view('news.index', compact('items', 'menu'));
    }

    public function frontendShow($menu, $slug)
    {
        $item = NewsItem::where('slug', $slug)->where('is_active', 1)->firstOrFail();

        $related = NewsItem::where('is_active', 1)
            ->where('id', '!=', $item->id)
            ->latest('news_date')
            ->take(3)
            ->get();

        return view('news.show', compact('item', 'related', 'menu'));
    }
}