<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MedicalJournal;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MedicalJournalController extends Controller
{
    public function index()
    {
        $menu = Menu::where('slug', 'medical-journals')->firstOrFail();
        $groupedJournals = MedicalJournal::orderBy('year', 'desc')
            ->orderBy('order', 'asc')
            ->get()
            ->groupBy('year');

        return view('admin.medical-journals.index', compact('menu', 'groupedJournals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pdf' => 'required|mimes:pdf|max:51200',
            'title' => 'required|string',
            'year' => 'required|integer'
        ]);

        try {
            $slug = $this->generateUniqueFilename($request->title);
            $filename = $slug . '.pdf';

            $request->file('pdf')->storeAs("journals/{$request->year}", $filename, 'public');

            MedicalJournal::create([
                'title' => $request->title,
                'filename' => $filename,
                'year' => $request->year,
                'is_active' => 1,
                'order' => (MedicalJournal::where('year', $request->year)->max('order') ?? 0) + 1
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, MedicalJournal $medicalJournal)
    {
        $request->validate([
            'pdf' => 'nullable|mimes:pdf|max:51200',
            'title' => 'required|string',
            'year' => 'required|integer'
        ]);

        try {
            $oldYear = $medicalJournal->year;
            $oldFilename = $medicalJournal->filename;
            $newSlug = $this->generateUniqueFilename($request->title, $medicalJournal->id);
            $newFilename = $newSlug . '.pdf';

            if ($request->hasFile('pdf')) {
                Storage::disk('public')->delete("journals/{$oldYear}/{$oldFilename}");
                $request->file('pdf')->storeAs("journals/{$request->year}", $newFilename, 'public');
            } elseif ($oldYear != $request->year || $oldFilename != $newFilename) {
                if (!Storage::disk('public')->exists("journals/{$request->year}")) {
                    Storage::disk('public')->makeDirectory("journals/{$request->year}");
                }
                Storage::disk('public')->move("journals/{$oldYear}/{$oldFilename}", "journals/{$request->year}/{$newFilename}");
            }

            $medicalJournal->update([
                'title' => $request->title,
                'filename' => $newFilename,
                'year' => $request->year,
                'is_active' => $request->input('is_active') == 1 ? 1 : 0
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function updateOrder(Request $request)
    {
        foreach ($request->orders as $item) {
            MedicalJournal::where('id', $item['id'])->update(['order' => $item['order']]);
        }
        return response()->json(['success' => true]);
    }

    public function delete(MedicalJournal $medicalJournal)
    {
        try {
            Storage::disk('public')->delete("journals/{$medicalJournal->year}/{$medicalJournal->filename}");
            $medicalJournal->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function generateUniqueFilename($title, $ignoreId = null)
    {
        $base = Str::slug(str_replace('&', 'and', $title));
        $slug = $base;
        $counter = 2;

        while (
            MedicalJournal::where('filename', $slug . '.pdf')
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $counter;
            $counter++;
        }
        return $slug;
    }

    public function servePdf($path, $year, $filename)
    {
        $storagePath = storage_path("app/public/journals/{$year}/{$filename}");

        abort_if(!file_exists($storagePath), 404);

        return response()->file($storagePath, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    public function frontendIndex($menu)
    {
        $groupedJournals = MedicalJournal::where('is_active', 1)
            ->orderBy('year', 'desc')
            ->orderBy('order', 'asc')
            ->get()
            ->groupBy('year');

        return view('medical-journals.index', compact('groupedJournals', 'menu'));
    }
}