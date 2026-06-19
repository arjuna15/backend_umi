<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminContentController extends Controller
{
    public function index()
    {
        return response()->json(\App\Models\Content::all());
    }

    public function update(Request $request)
    {
        $request->validate([
            'contents' => 'required|array',
        ]);

        foreach ($request->contents as $key => $value) {
            \App\Models\Content::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return response()->json(['message' => 'Contents updated']);
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.webp';
            
            // Ensure uploads directory exists
            $uploadPath = public_path('uploads');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            try {
                $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                $image = $manager->read($file);
                $image->toWebp(80)->save($uploadPath . '/' . $filename);
                
                return response()->json([
                    'url' => url('uploads/' . $filename)
                ]);
            } catch (\Exception $e) {
                // Fallback to regular upload if Intervention fails
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move($uploadPath, $filename);
                return response()->json([
                    'url' => url('uploads/' . $filename)
                ]);
            }
        }
        
        return response()->json(['message' => 'No image uploaded'], 400);
    }
}
