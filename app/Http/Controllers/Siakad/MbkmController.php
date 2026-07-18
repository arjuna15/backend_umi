<?php

namespace App\Http\Controllers\Siakad;

use App\Http\Controllers\Controller;
use App\Models\MbkmProgram;
use App\Models\MbkmSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MbkmController extends Controller
{
    /**
     * List all MBKM programs with submission count.
     */
    public function index(Request $request): JsonResponse
    {
        $programs = MbkmProgram::withCount('submissions')->orderByDesc('created_at')->get();
        
        $mySubmissions = [];
        if ($request->user()) {
            $mySubmissions = MbkmSubmission::with('program')
                ->where('user_id', $request->user()->id)
                ->orderByDesc('created_at')
                ->get();
        }

        return response()->json([
            'programs' => $programs,
            'my_submissions' => $mySubmissions
        ]);
    }

    /**
     * Create a new MBKM program (admin only).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sks' => 'required|integer|min:1',
            'period' => 'required|string|max:100',
            'status' => 'in:draft,active,closed',
        ]);

        $program = MbkmProgram::create($validated);

        return response()->json($program, 201);
    }

    /**
     * Get program details with submissions.
     */
    public function show($id): JsonResponse
    {
        $program = MbkmProgram::with(['submissions.user:id,name,nim_nip,prodi', 'submissions.approver:id,name'])
            ->findOrFail($id);

        return response()->json($program);
    }

    /**
     * Student submits application to a program.
     */
    public function submit($id, Request $request): JsonResponse
    {
        $program = MbkmProgram::findOrFail($id);

        $existing = MbkmSubmission::where('mbkm_program_id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Anda sudah mendaftar pada program ini.'], 422);
        }

        $request->validate([
            'notes' => 'nullable|string',
        ]);

        $submission = MbkmSubmission::create([
            'mbkm_program_id' => $program->id,
            'user_id' => $request->user()->id,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        $submission->load('user:id,name,nim_nip');

        return response()->json($submission, 201);
    }

    /**
     * Admin approves or rejects a submission.
     */
    public function approve($submissionId, Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string',
        ]);

        $submission = MbkmSubmission::findOrFail($submissionId);

        $submission->update([
            'status' => $request->status,
            'notes' => $request->notes ?? $submission->notes,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        $submission->load(['user:id,name', 'approver:id,name', 'program']);

        return response()->json($submission);
    }

    /**
     * Delete a program.
     */
    public function destroy($id): JsonResponse
    {
        $program = MbkmProgram::findOrFail($id);
        $program->delete();

        return response()->json(['message' => 'Program berhasil dihapus.']);
    }
}
