<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::orderBy('order')->get();
        $links = Menu::getFunctionalLeafMenus();
        return view('admin.sliders.index', compact('sliders', 'links'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120',
            'header_1' => 'required|string',
            'header_2' => 'required|string',
            'description' => 'required|string',
            'link_url' => 'required|string',
        ]);

        $file = $request->file('image');
        $fileName = time() . '.webp';
        $path = "sliders/{$fileName}";

        $this->processSliderImage($file->getRealPath(), storage_path("app/public/{$path}"));

        Slider::create([
            'image_path' => $path,
            'header_1' => $request->header_1,
            'header_2' => $request->header_2,
            'description' => $request->description,
            'link_url' => $request->link_url,
            'order' => Slider::max('order') + 1
        ]);

        return back()->with('success', 'Slider added successfully');
    }

    public function update(Request $request, Slider $slider)
    {
        $data = $request->validate([
            'header_1' => 'required|string',
            'header_2' => 'required|string',
            'description' => 'required|string',
            'link_url' => 'required|string',
            'is_active' => 'required|boolean'
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($slider->image_path);
            $fileName = time() . '.webp';
            $path = "sliders/{$fileName}";
            $this->processSliderImage($request->file('image')->getRealPath(), storage_path("app/public/{$path}"));
            $slider->image_path = $path;
        }

        $slider->update($data);
        return response()->json(['success' => true]);
    }

    public function updateOrder(Request $request)
    {
        foreach ($request->orders as $item) {
            Slider::where('id', $item['id'])->update(['order' => $item['order']]);
        }
        return response()->json(['success' => true]);
    }

    public function destroy(Slider $slider)
    {
        Storage::disk('public')->delete($slider->image_path);
        $slider->delete();
        return back()->with('success', 'Slider deleted');
    }

    private function processSliderImage($source, $destination)
    {
        $info = getimagesize($source);
        $src = match ($info[2]) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($source),
            IMAGETYPE_PNG => imagecreatefrompng($source),
            IMAGETYPE_WEBP => imagecreatefromwebp($source),
            default => throw new Exception('Unsupported image type'),
        };

        $width = $info[0];
        $height = $info[1];
        $targetRatio = 10 / 4;
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

        $dst = imagecreatetruecolor(1400, 560);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, $srcX, $srcY, 1400, 560, $cropWidth, $cropHeight);
        imagewebp($dst, $destination, 85);
        imagedestroy($src);
        imagedestroy($dst);
    }
}