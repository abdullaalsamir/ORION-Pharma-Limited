<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HalfYearlyReports;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HalfYearlyReportsController extends Controller
{
    public function index()
    {
        $menu = Menu::where('slug', 'half-yearly-reports')->firstOrFail();
        $items = HalfYearlyReports::orderBy('publication_date', 'desc')
            ->orderBy('order', 'desc')
            ->get();

        return view('admin.half-yearly-reports.index', compact('menu', 'items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pdf' => 'required|mimes:pdf|max:51200',
            'title' => 'required|string',
            'publication_date' => 'required|date',
            'description' => 'nullable|string|max:550'
        ]);

        $slug = $this->generateUniqueFilename($request->title);
        $filename = $slug . '.pdf';

        $request->file('pdf')->storeAs('half-yearly-reports', $filename, 'public');

        HalfYearlyReports::create([
            'title' => $request->title,
            'filename' => $filename,
            'description' => $request->description,
            'publication_date' => $request->publication_date,
            'is_active' => 1,
            'order' => HalfYearlyReports::max('order') + 1
        ]);

        return back()->with('success', 'Information added successfully');
    }

    public function update(Request $request, HalfYearlyReports $halfYearlyReport)
    {
        $request->validate([
            'pdf' => 'nullable|mimes:pdf|max:51200',
            'title' => 'required|string',
            'publication_date' => 'required|date',
            'description' => 'nullable|string|max:550'
        ]);

        $oldFilename = $halfYearlyReport->filename;

        $slug = ($halfYearlyReport->title === $request->title)
            ? str_replace('.pdf', '', $oldFilename)
            : $this->generateUniqueFilename($request->title, $halfYearlyReport->id);

        $newFilename = $slug . '.pdf';

        if ($request->hasFile('pdf')) {
            Storage::disk('public')->delete("half-yearly-reports/{$oldFilename}");
            $request->file('pdf')->storeAs('half-yearly-reports', $newFilename, 'public');
        } elseif ($oldFilename !== $newFilename) {
            Storage::disk('public')->move(
                "half-yearly-reports/{$oldFilename}",
                "half-yearly-reports/{$newFilename}"
            );
        }

        $halfYearlyReport->update([
            'title' => $request->title,
            'filename' => $newFilename,
            'description' => $request->description,
            'publication_date' => $request->publication_date,
            'is_active' => $request->input('is_active') == 1 ? 1 : 0
        ]);

        return response()->json(['success' => true]);
    }

    public function updateOrder(Request $request)
    {
        foreach ($request->orders as $item) {
            HalfYearlyReports::where('id', $item['id'])
                ->update(['order' => $item['order']]);
        }

        return response()->json(['success' => true]);
    }

    public function delete(HalfYearlyReports $halfYearlyReport)
    {
        Storage::disk('public')->delete(
            "half-yearly-reports/{$halfYearlyReport->filename}"
        );

        $halfYearlyReport->delete();

        return back()->with('success', 'Deleted successfully');
    }

    private function generateUniqueFilename($title, $ignoreId = null)
    {
        $base = Str::slug(str_replace('&', 'and', $title));
        $slug = $base;
        $counter = 2;

        while (
            HalfYearlyReports::where('filename', $slug . '.pdf')
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }

    public function servePdf($path, $filename)
    {
        $storagePath = storage_path("app/public/half-yearly-reports/{$filename}");

        if (!file_exists($storagePath)) {
            abort(404);
        }

        return response()->file($storagePath, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    public function frontendIndex($menu)
    {
        $items = HalfYearlyReports::where('is_active', 1)
            ->orderBy('publication_date', 'desc')
            ->orderBy('order', 'desc')
            ->get();

        return view('half-yearly-reports.index', compact('items', 'menu'));
    }
}
