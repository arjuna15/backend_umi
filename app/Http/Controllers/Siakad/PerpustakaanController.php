<?php

namespace App\Http\Controllers\Siakad;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PerpustakaanController extends Controller
{
    private function getConfigPath()
    {
        return storage_path('app/slims_config.json');
    }

    private function getConfig()
    {
        $path = $this->getConfigPath();
        if (!file_exists($path)) {
            return null;
        }
        return json_decode(file_get_contents($path), true);
    }

    public function config()
    {
        $config = $this->getConfig();

        if (!$config) {
            return response()->json(['data' => [
                'base_url' => null,
                'api_key' => null,
                'enabled' => false,
            ]]);
        }

        return response()->json(['data' => $config]);
    }

    public function updateConfig(Request $request)
    {
        $validated = $request->validate([
            'base_url' => 'required|url|max:500',
            'api_key' => 'nullable|string|max:500',
            'enabled' => 'nullable|boolean',
        ]);

        file_put_contents($this->getConfigPath(), json_encode($validated, JSON_PRETTY_PRINT));

        return response()->json(['data' => $validated, 'message' => 'Configuration updated']);
    }

    public function testConnection(Request $request)
    {
        $config = $this->getConfig();

        if (!$config || empty($config['base_url'])) {
            return response()->json(['success' => false, 'message' => 'No configuration found'], 400);
        }

        try {
            $response = Http::timeout(10)->get($config['base_url']);

            return response()->json([
                'success' => $response->successful(),
                'message' => $response->successful() ? 'Connection to sLimS successful' : 'Connection failed',
                'status_code' => $response->status(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
            ], 400);
        }
    }

    public function search(Request $request)
    {
        $config = $this->getConfig();

        if (!$config || empty($config['base_url'])) {
            return response()->json(['error' => 'sLimS not configured'], 400);
        }

        $request->validate([
            'keyword' => 'required|string|max:255',
        ]);

        try {
            $response = Http::timeout(10)->get($config['base_url'] . '/api/search', [
                'keyword' => $request->keyword,
            ]);

            return response()->json(['data' => $response->json()]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Search failed: ' . $e->getMessage()], 500);
        }
    }

    public function stats()
    {
        $config = $this->getConfig();

        if (!$config || empty($config['base_url'])) {
            return response()->json(['data' => ['message' => 'sLimS not configured']]);
        }

        try {
            $response = Http::timeout(10)->get($config['base_url'] . '/api/stats');

            return response()->json(['data' => $response->json()]);
        } catch (\Exception $e) {
            return response()->json(['data' => ['message' => 'Unable to fetch stats: ' . $e->getMessage()]]);
        }
    }
}
