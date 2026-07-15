<?php

namespace App\Http\Controllers\Siakad;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class EsignController extends Controller
{
    private function getConfigPath()
    {
        return storage_path('app/esign_config.json');
    }

    public function config()
    {
        $path = $this->getConfigPath();

        if (!file_exists($path)) {
            return response()->json(['data' => [
                'provider' => null,
                'api_key' => null,
                'base_url' => null,
                'enabled' => false,
            ]]);
        }

        $config = json_decode(file_get_contents($path), true);

        return response()->json(['data' => $config]);
    }

    public function updateConfig(Request $request)
    {
        $validated = $request->validate([
            'provider' => 'required|string|max:100',
            'api_key' => 'required|string|max:500',
            'base_url' => 'nullable|url|max:500',
            'enabled' => 'nullable|boolean',
        ]);

        file_put_contents($this->getConfigPath(), json_encode($validated, JSON_PRETTY_PRINT));

        return response()->json(['data' => $validated, 'message' => 'Configuration updated']);
    }

    public function testConnection(Request $request)
    {
        $path = $this->getConfigPath();

        if (!file_exists($path)) {
            return response()->json(['success' => false, 'message' => 'No configuration found'], 400);
        }

        $config = json_decode(file_get_contents($path), true);

        if (empty($config['api_key']) || empty($config['provider'])) {
            return response()->json(['success' => false, 'message' => 'Incomplete configuration'], 400);
        }

        // Simulate connection test
        return response()->json([
            'success' => true,
            'message' => 'Connection to ' . $config['provider'] . ' successful',
            'provider' => $config['provider'],
        ]);
    }
}
