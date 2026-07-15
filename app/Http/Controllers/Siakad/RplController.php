<?php

namespace App\Http\Controllers\Siakad;

use App\Http\Controllers\Controller;
use App\Models\RplApplication;
use App\Models\RplDocument;
use Illuminate\Http\Request;

class RplController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = RplApplication::with('user', 'documents');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('applicant_name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        if (!in_array($user->role, ['admin', 'superadmin'])) {
            $query->where('user_id', $user->id);
        }

        $list = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

        return response()->json(['data' => $list]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'applicant_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'previous_institution' => 'required|string|max:255',
            'previous_program' => 'required|string|max:255',
            'target_program' => 'required|string|max:255',
            'work_experience_years' => 'nullable|integer|min:0',
            'document_path' => 'nullable|string',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['status'] = 'pending';

        $application = RplApplication::create($validated);

        return response()->json(['data' => $application], 201);
    }

    public function show($id)
    {
        $application = RplApplication::with('user', 'documents', 'reviewer')->findOrFail($id);

        return response()->json(['data' => $application]);
    }

    public function review($id, Request $request)
    {
        $application = RplApplication::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'credits_recognized' => 'nullable|integer|min:0',
            'reviewer_notes' => 'nullable|string',
        ]);

        $validated['reviewed_by'] = $request->user()->id;

        $application->update($validated);

        return response()->json(['data' => $application]);
    }

    public function uploadDocument($applicationId, Request $request)
    {
        $application = RplApplication::findOrFail($applicationId);

        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'type' => 'required|string|max:100',
        ]);

        $path = $request->file('file')->store('rpl_documents', 'public');

        $doc = RplDocument::create([
            'rpl_application_id' => $application->id,
            'type' => $request->type,
            'file_path' => $path,
            'original_name' => $request->file('file')->getClientOriginalName(),
        ]);

        return response()->json(['data' => $doc], 201);
    }

    public function stats()
    {
        $byStatus = RplApplication::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalCredits = RplApplication::where('status', 'approved')->sum('credits_recognized');

        return response()->json(['data' => [
            'by_status' => $byStatus,
            'total_credits_recognized' => $totalCredits,
            'total_applications' => RplApplication::count(),
        ]]);
    }
}
