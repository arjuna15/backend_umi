<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminTestimonialController extends Controller
{
    public function index()
    {
        return response()->json(\App\Models\Testimonial::orderBy('id', 'desc')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'image_url' => 'required|string',
        ]);

        $testi = \App\Models\Testimonial::create($validated);
        return response()->json($testi, 201);
    }

    public function destroy($id)
    {
        $testi = \App\Models\Testimonial::findOrFail($id);
        $testi->delete();
        return response()->json(['message' => 'Testimonial deleted']);
    }
}
