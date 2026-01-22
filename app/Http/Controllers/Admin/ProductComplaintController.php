<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductComplaint;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProductComplaintMail;

class ProductComplaintController extends Controller
{
    public function index()
    {
        $menu = Menu::where('slug', 'product-complaint')->firstOrFail();
        $complaints = ProductComplaint::latest()->get();
        return view('admin.product-complaint.index', compact('menu', 'complaints'));
    }

    public function frontendIndex($menu)
    {
        $raisingDate = now()->format('d/m/Y');
        try {
            $response = Http::get('http://worldtimeapi.org/api/timezone/Asia/Dhaka');
            if ($response->successful()) {
                $raisingDate = date('d/m/Y', strtotime($response->json('datetime')));
            }
        } catch (\Exception $e) {
        }

        return view('product-complaint.index', compact('menu', 'raisingDate'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required',
            'batch_number' => 'required',
            'complainant_name' => 'required',
            'contact_number' => 'required',
        ]);

        $data = $request->all();
        $data['complaint_date'] = now()->toDateString();

        $complaint = ProductComplaint::create($data);

        try {
            Mail::to(config('mail.from.address'))->send(new ProductComplaintMail($complaint));
        } catch (\Exception $e) {
            \Log::error('Mail Error: ' . $e->getMessage());
        }

        return back()->with('success', 'Your complaint has been submitted successfully and an email has been sent to our Quality Assurance team.');
    }

    public function delete(ProductComplaint $complaint)
    {
        $complaint->delete();
        return back()->with('success', 'Record deleted.');
    }
}