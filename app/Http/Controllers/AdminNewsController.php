<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminNewsController extends Controller
{
    public function index()
    {
        return response()->json(\App\Models\News::orderBy('id', 'desc')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|string|max:255',
            'image_url' => 'required|string',
            'source' => 'nullable|string|max:255'
        ]);

        $news = \App\Models\News::create($validated);
        return response()->json($news, 201);
    }

    public function update(Request $request, $id)
    {
        $news = \App\Models\News::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|string|max:255',
            'image_url' => 'required|string',
            'source' => 'nullable|string|max:255'
        ]);

        $news->update($validated);
        return response()->json($news);
    }

    public function destroy($id)
    {
        $news = \App\Models\News::findOrFail($id);
        $news->delete();
        return response()->json(['message' => 'News deleted']);
    }
}
