<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/home-data', function () {
    return response()->json([
        'news' => \App\Models\News::orderBy('id', 'asc')->limit(3)->get(),
        'testimonials' => \App\Models\Testimonial::all(),
        'contents' => \App\Models\Content::pluck('value', 'key')
    ]);
});

Route::get('/news', function () {
    return response()->json([
        'news' => \App\Models\News::orderBy('id', 'asc')->get()
    ]);
});

Route::get('/contents', [\App\Http\Controllers\AdminContentController::class, 'index']);

// Admin Auth Routes
Route::post('/admin/login', [\App\Http\Controllers\AuthController::class, 'login']);

// Protected Admin Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/admin/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
    
    // News
    Route::get('/admin/news', [\App\Http\Controllers\AdminNewsController::class, 'index']);
    Route::post('/admin/news', [\App\Http\Controllers\AdminNewsController::class, 'store']);
    Route::put('/admin/news/{id}', [\App\Http\Controllers\AdminNewsController::class, 'update']);
    Route::delete('/admin/news/{id}', [\App\Http\Controllers\AdminNewsController::class, 'destroy']);
    
    // Testimonials
    Route::get('/admin/testimonials', [\App\Http\Controllers\AdminTestimonialController::class, 'index']);
    Route::post('/admin/testimonials', [\App\Http\Controllers\AdminTestimonialController::class, 'store']);
    Route::delete('/admin/testimonials/{id}', [\App\Http\Controllers\AdminTestimonialController::class, 'destroy']);
    
    // Contents & Images
    Route::put('/admin/contents', [\App\Http\Controllers\AdminContentController::class, 'update']);
    Route::post('/admin/upload-image', [\App\Http\Controllers\AdminContentController::class, 'uploadImage']);
});

