<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/home-data', function () {
    return response()->json([
        'news' => \App\Models\News::orderBy('created_at', 'desc')->get(),
        'testimonials' => \App\Models\Testimonial::all()
    ]);
});
