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
        $sessions = ProctorSession::with(['user:id,name,nim_nip', 'quiz.course'])
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
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id'
        ]);

        $session = ProctorSession::create([
            'quiz_id' => $request->quiz_id,
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
            'token' => 'required|string',
            'event_type' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $session = ProctorSession::where('token', strtoupper($request->token))->first();

        if (!$session) {
            return response()->json(['success' => false, 'message' => 'Sesi tidak ditemukan.'], 404);
        }

        $log = ProctorLog::create([
            'proctor_session_id' => $session->id,
            'user_id' => $request->user()->id,
            'event' => $request->event_type,
            'data' => ['description' => $request->description],
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

    /**
     * Join proctoring session (Student).
     */
    public function join(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $session = ProctorSession::where('token', strtoupper($request->token))->first();

        if (!$session) {
            return response()->json(['success' => false, 'message' => 'Token tidak valid.'], 404);
        }

        if ($session->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'Sesi belum dimulai atau sudah berakhir.'], 422);
        }

        return response()->json([
            'success' => true,
            'session' => $session
        ]);
    }

    /**
     * Get list of quizzes that require proctoring.
     */
    public function getAvailableQuizzes(Request $request): JsonResponse
    {
        $quizzes = \App\Models\Quiz::with('course')
            ->where('require_proctoring', true)
            ->get()
            ->map(function ($quiz) {
                return [
                    'id' => $quiz->id,
                    'title' => $quiz->title,
                    'category' => $quiz->category,
                    'course_name' => $quiz->course?->name ?? 'Mata Kuliah'
                ];
            });

        return response()->json($quizzes);
    }
}
