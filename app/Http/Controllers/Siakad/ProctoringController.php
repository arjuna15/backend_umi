<?php

namespace App\Http\Controllers\Siakad;

use App\Http\Controllers\Controller;
use App\Models\ProctorLog;
use App\Models\ProctorSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProctoringController extends Controller
{
    /**
     * List all proctor sessions.
     */
    public function index(Request $request): JsonResponse
    {
        $sessions = ProctorSession::with('user:id,name,nim_nip')
            ->withCount('logs')
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json($sessions);
    }

    /**
     * Generate a unique token for an exam session.
     */
    public function generateToken(Request $request): JsonResponse
    {
        $quizId = $request->input('quiz_id', 1);

        $session = ProctorSession::create([
            'quiz_id' => $quizId,
            'user_id' => $request->user()->id,
            'token' => strtoupper(Str::random(8)),
            'status' => 'waiting',
        ]);

        return response()->json([
            'success' => true,
            'token' => $session->token,
            'session' => $session
        ], 201);
    }

    /**
     * Start proctoring session.
     */
    public function start($id): JsonResponse
    {
        $session = ProctorSession::findOrFail($id);

        if ($session->status !== 'waiting') {
            return response()->json(['success' => false, 'message' => 'Session sudah dimulai atau sudah berakhir.'], 422);
        }

        $session->update([
            'status' => 'active',
            'started_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'session' => $session
        ]);
    }

    /**
     * Stop proctoring session.
     */
    public function stop($id): JsonResponse
    {
        $session = ProctorSession::findOrFail($id);

        if ($session->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'Session tidak dalam status aktif.'], 422);
        }

        $session->update([
            'status' => 'ended',
            'ended_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'session' => $session
        ]);
    }

    /**
     * Store a proctor log event.
     */
    public function logEvent(Request $request): JsonResponse
    {
        $request->validate([
            'proctor_session_id' => 'required|exists:proctor_sessions,id',
            'event' => 'required|string|max:100',
            'data' => 'nullable|array',
        ]);

        $log = ProctorLog::create([
            'proctor_session_id' => $request->proctor_session_id,
            'user_id' => $request->user()->id,
            'event' => $request->event,
            'data' => $request->data,
        ]);

        return response()->json([
            'success' => true,
            'log' => $log
        ], 201);
    }

    /**
     * Get all logs for a session.
     */
    public function getSessionLogs($id): JsonResponse
    {
        $session = ProctorSession::with(['logs.user:id,name,nim_nip', 'user:id,name'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'session' => $session,
            'logs' => $session->logs
        ]);
    }
}
