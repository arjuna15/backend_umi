<?php

namespace App\Http\Controllers\Siakad;

use App\Http\Controllers\Controller;
use App\Models\ApiToken;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiTokenController extends Controller
{
    public function index()
    {
        $tokens = ApiToken::orderBy('created_at', 'desc')->get();

        return response()->json(['data' => $tokens]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'rate_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $validated['token'] = Str::random(64);
        $validated['created_by'] = $request->user()->id;
        $validated['is_active'] = true;

        $token = ApiToken::create($validated);

        // Return token only on creation so it can be copied
        return response()->json([
            'data' => $token,
            'plain_token' => $validated['token'],
        ], 201);
    }

    public function destroy($id)
    {
        $token = ApiToken::findOrFail($id);
        $token->delete();

        return response()->json(['message' => 'API token deleted']);
    }

    public function toggle($id)
    {
        $token = ApiToken::findOrFail($id);
        $token->update(['is_active' => !$token->is_active]);

        return response()->json(['data' => $token]);
    }
}
