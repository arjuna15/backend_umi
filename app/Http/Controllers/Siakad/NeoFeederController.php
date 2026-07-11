<?php

namespace App\Http\Controllers\Siakad;

use App\Http\Controllers\Controller;
use App\Jobs\SyncToFeederJob;
use App\Models\Course;
use App\Models\Grade;
use App\Models\KrsSubmission;
use App\Models\User;
use App\Services\NeoFeederService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class NeoFeederController extends Controller
{
    protected NeoFeederService $feederService;

    public function __construct(NeoFeederService $feederService)
    {
        $this->feederService = $feederService;
    }

    /**
     * Test WS connection to Neo Feeder
     */
    public function testConnection(): JsonResponse
    {
        try {
            $token = $this->feederService->getToken();
            return response()->json([
                'success' => true,
                'message' => 'Successfully connected to Neo Feeder WS!',
                'token' => substr($token, 0, 15) . '...',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get statistics of synced/unsynced data
     */
    public function getStats(): JsonResponse
    {
        $studentsTotal = User::where('role', 'mahasiswa')->count();
        $studentsSynced = User::where('role', 'mahasiswa')->whereNotNull('feeder_id')->count();

        $coursesTotal = Course::count();
        $coursesSynced = Course::whereNotNull('feeder_id')->count();

        $krsTotal = KrsSubmission::count();
        $krsSynced = KrsSubmission::whereNotNull('feeder_id')->count();

        $gradesTotal = Grade::count();
        $gradesSynced = Grade::whereNotNull('feeder_id')->count();

        return response()->json([
            'success' => true,
            'stats' => [
                'mahasiswa' => [
                    'total' => $studentsTotal,
                    'synced' => $studentsSynced,
                    'unsynced' => $studentsTotal - $studentsSynced,
                ],
                'courses' => [
                    'total' => $coursesTotal,
                    'synced' => $coursesSynced,
                    'unsynced' => $coursesTotal - $coursesSynced,
                ],
                'krs' => [
                    'total' => $krsTotal,
                    'synced' => $krsSynced,
                    'unsynced' => $krsTotal - $krsSynced,
                ],
                'grades' => [
                    'total' => $gradesTotal,
                    'synced' => $gradesSynced,
                    'unsynced' => $gradesTotal - $gradesSynced,
                ],
            ]
        ]);
    }

    /**
     * Trigger sync queue for specific category
     */
    public function triggerSync(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:mahasiswa,courses,krs,grades',
        ]);

        $type = $request->input('type');
        $ids = [];

        switch ($type) {
            case 'mahasiswa':
                $ids = User::where('role', 'mahasiswa')->whereNull('feeder_id')->pluck('id')->toArray();
                break;
            case 'courses':
                $ids = Course::whereNull('feeder_id')->pluck('id')->toArray();
                break;
            case 'krs':
                $ids = KrsSubmission::whereNull('feeder_id')->pluck('id')->toArray();
                break;
            case 'grades':
                $ids = Grade::whereNull('feeder_id')->pluck('id')->toArray();
                break;
        }

        if (empty($ids)) {
            return response()->json([
                'success' => true,
                'message' => "All items in [{$type}] are already synced!",
            ]);
        }

        // Dispatch background sync job
        SyncToFeederJob::dispatch($type, $ids);

        return response()->json([
            'success' => true,
            'message' => "Successfully queued " . count($ids) . " items for [{$type}] sync in the background.",
        ]);
    }
}
