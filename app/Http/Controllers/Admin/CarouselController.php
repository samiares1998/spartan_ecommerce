<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Carousel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CarouselController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Carousel',
            'carousels' => Carousel::orderByDesc('created_at')->get()
        ];

        return view('admin.carousel.index', $data);
    }

    public function create()
    {
        return view('admin.carousel.create', [
            'title' => 'Create Carousel Slide'
        ]);
    }

    public function check(Request $request)
    {
        $exists = Carousel::where('title', $request->title)->exists();

        return response()->json([
            'status' => 'success',
            'messages' => $exists ? 'not available' : 'available'
        ], $exists ? 200 : 201);
    }

    public function save(Request $request)
    {
        $validators = Validator::make($request->all(), [
            'title' => 'required|unique:carousels',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'video' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if ($validators->fails()) {
            return redirect()->route('carouselCreate')->withErrors($validators)->withInput();
        }

        $imageName = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = Str::random(20) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('carousel/slides'), $imageName);
        }

        Carousel::create([
            'shop_id' => Auth::user()->shop->id,
            'title' => $request->title,
            'image' => $imageName,
            'video' => $request->video,
            'description' => $request->description,
        ]);

        return redirect()->route('carousel')->with('success', 'Slide created successfully!');
    }

    public function delete($id)
    {
        $carousel = Carousel::findOrFail($id);

        if ($carousel->image && file_exists(public_path('carousel/slides/' . $carousel->image))) {
            unlink(public_path('carousel/slides/' . $carousel->image));
        }

        $carousel->delete();

        return redirect()->route('carousel')->with('success', 'Slide deleted successfully.');
    }
}
