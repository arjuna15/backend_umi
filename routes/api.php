<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/home-data', function () {
    return response()->json([
        'news' => \App\Models\News::orderBy('id', 'asc')->limit(3)->get(),
        'testimonials' => \App\Models\Testimonial::all()
    ]);
});

Route::get('/news', function () {
    return response()->json([
        'news' => \App\Models\News::orderBy('id', 'asc')->get()
    ]);
});
