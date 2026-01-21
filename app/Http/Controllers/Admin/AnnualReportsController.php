<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnnualReports;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AnnualReportsController extends Controller
{
    public function index()
    {
        $menu = Menu::where('slug', 'annual-reports')->firstOrFail();
        $items = AnnualReports::orderBy('publication_date', 'desc')
            ->orderBy('order', 'desc')
            ->get();

        return view('admin.annual-reports.index', compact('menu', 'items'));
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

        $request->file('pdf')->storeAs("annual-reports", $filename, 'public');

        AnnualReports::create([
            'title' => $request->title,
            'filename' => $filename,
            'description' => $request->description,
            'publication_date' => $request->publication_date,
            'is_active' => 1,
            'order' => AnnualReports::max('order') + 1
        ]);

        return back()->with('success', 'Information added successfully');
    }

    public function update(Request $request, AnnualReports $annualReports)
    {
        $request->validate([
            'pdf' => 'nullable|mimes:pdf|max:51200',
            'title' => 'required|string',
            'publication_date' => 'required|date',
            'description' => 'nullable|string|max:550'
        ]);

        $oldFilename = $annualReports->filename;
        $slug = ($annualReports->title === $request->title)
            ? str_replace('.pdf', '', $oldFilename)
            : $this->generateUniqueFilename($request->title, $annualReports->id);

        $newFilename = $slug . '.pdf';

        if ($request->hasFile('pdf')) {
            Storage::disk('public')->delete("annual-reports/{$oldFilename}");
            $request->file('pdf')->storeAs("annual-reports", $newFilename, 'public');
        } elseif ($oldFilename != $newFilename) {
            Storage::disk('public')->move("annual-reports/{$oldFilename}", "annual-reports/{$newFilename}");
        }

        $annualReports->update([
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
            AnnualReports::where('id', $item['id'])->update(['order' => $item['order']]);
        }
        return response()->json(['success' => true]);
    }

    public function delete(AnnualReports $annualReports)
    {
        Storage::disk('public')->delete("annual-reports/{$annualReports->filename}");
        $annualReports->delete();
        return back()->with('success', 'Deleted successfully');
    }

    private function generateUniqueFilename($title, $ignoreId = null)
    {
        $base = Str::slug(str_replace('&', 'and', $title));
        $slug = $base;
        $counter = 2;
        while (
            AnnualReports::where('filename', $slug . '.pdf')
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $counter;
            $counter++;
        }
        return $slug;
    }

    public function servePdf($path, $filename)
    {
        $storagePath = storage_path("app/public/annual-reports/{$filename}");

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
        $items = AnnualReports::where('is_active', 1)
            ->orderBy('publication_date', 'desc')
            ->orderBy('order', 'desc')
            ->get();

        return view('annual-reports.index', compact('items', 'menu'));
    }
}