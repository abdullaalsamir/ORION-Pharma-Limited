<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class ScholarshipController extends Controller
{
    public function index()
    {
        $menu = Menu::where('slug', 'scholarship')->firstOrFail();
        $items = Scholarship::orderBy('order', 'asc')->get();
        return view('admin.scholarship.index', compact('menu', 'items'));
    }

    private function formatPrefix($value, $prefix)
    {
        if (empty($value))
            return null;

        $cleanValue = str_replace($prefix . ': ', '', $value);
        $cleanValue = trim($cleanValue);

        if (empty($cleanValue))
            return null;

        return $prefix . ': ' . $cleanValue;
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'medical_college' => 'required|string',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120'
        ]);

        try {
            $file = $request->file('image');
            $fileName = Str::slug($request->name) . '-' . time() . '.webp';
            $path = "scholarship/{$fileName}";

            if (!Storage::disk('public')->exists('scholarship')) {
                Storage::disk('public')->makeDirectory('scholarship');
            }

            $this->processScholarImage($file->getRealPath(), storage_path("app/public/{$path}"));

            Scholarship::create([
                'name' => $request->name,
                'session' => $this->formatPrefix($request->input('session'), 'Session'), // Changed this
                'roll_no' => $request->roll_no,
                'medical_college' => $request->medical_college,
                'image_path' => $path,
                'order' => Scholarship::max('order') + 1,
                'is_active' => 1
            ]);

            return back()->with('success', 'Scholar added successfully');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, Scholarship $scholarship)
    {
        $data = [
            'name' => $request->name,
            'session' => $this->formatPrefix($request->input('session'), 'Session'), // Changed this
            'roll_no' => $request->roll_no,
            'medical_college' => $request->medical_college,
            'is_active' => $request->is_active
        ];

        if ($request->hasFile('image')) {
            if (Storage::disk('public')->exists($scholarship->image_path)) {
                Storage::disk('public')->delete($scholarship->image_path);
            }
            $fileName = Str::slug($request->name) . '-' . time() . '.webp';
            $path = "scholarship/{$fileName}";
            $this->processScholarImage($request->file('image')->getRealPath(), storage_path("app/public/{$path}"));
            $data['image_path'] = $path;
        }

        $scholarship->update($data);
        return response()->json(['success' => true]);
    }

    public function updateOrder(Request $request)
    {
        foreach ($request->orders as $item) {
            Scholarship::where('id', $item['id'])->update(['order' => $item['order']]);
        }
        return response()->json(['success' => true]);
    }

    public function delete(Scholarship $scholarship)
    {
        if (Storage::disk('public')->exists($scholarship->image_path)) {
            Storage::disk('public')->delete($scholarship->image_path);
        }

        $scholarship->delete();

        return back()->with('success', 'Scholarship record deleted successfully');
    }

    private function processScholarImage($sourcePath, $destinationPath)
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

        $targetRatio = 9 / 11;
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
        if ($finalWidth > 300) {
            $finalWidth = 300;
        }
        $finalHeight = $finalWidth / $targetRatio;

        $dst = imagecreatetruecolor($finalWidth, $finalHeight);

        imagealphablending($dst, false);
        imagesavealpha($dst, true);

        imagecopyresampled($dst, $src, 0, 0, $srcX, $srcY, $finalWidth, $finalHeight, $cropWidth, $cropHeight);

        if (!imagewebp($dst, $destinationPath, 100)) {
            throw new Exception('Failed to save WebP image.');
        }

        imagedestroy($src);
        imagedestroy($dst);
    }
}