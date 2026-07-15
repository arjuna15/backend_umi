<?php

namespace App\Http\Controllers\Siakad;

use App\Http\Controllers\Controller;
use App\Models\PpgParticipant;
use App\Models\PpgActivity;
use Illuminate\Http\Request;

class PpgController extends Controller
{
    public function index(Request $request)
    {
        $query = PpgParticipant::with('user');

        if ($request->has('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('nip', 'like', "%{$s}%");
            });
        }

        if ($request->has('batch')) {
            $query->where('batch', $request->batch);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $participants = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

        return response()->json(['data' => $participants]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'school_origin' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'batch' => 'required|string|max:50',
            'status' => 'nullable|in:registered,active,completed,dropped',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'certificate_number' => 'nullable|string|max:100',
        ]);

        $participant = PpgParticipant::create($validated);

        return response()->json(['data' => $participant], 201);
    }

    public function update($id, Request $request)
    {
        $participant = PpgParticipant::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'name' => 'sometimes|string|max:255',
            'nip' => 'nullable|string|max:50',
            'school_origin' => 'sometimes|string|max:255',
            'subject' => 'sometimes|string|max:255',
            'batch' => 'sometimes|string|max:50',
            'status' => 'sometimes|in:registered,active,completed,dropped',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'certificate_number' => 'nullable|string|max:100',
        ]);

        $participant->update($validated);

        return response()->json(['data' => $participant]);
    }

    public function activities($id)
    {
        $participant = PpgParticipant::findOrFail($id);

        $activities = PpgActivity::where('ppg_participant_id', $id)
            ->orderBy('date', 'desc')
            ->get();

        return response()->json(['data' => $activities]);
    }

    public function addActivity($id, Request $request)
    {
        $participant = PpgParticipant::findOrFail($id);

        $validated = $request->validate([
            'activity_type' => 'required|in:workshop,teaching_practice,exam,seminar',
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'score' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $validated['ppg_participant_id'] = $participant->id;

        $activity = PpgActivity::create($validated);

        return response()->json(['data' => $activity], 201);
    }

    public function stats()
    {
        $byStatus = PpgParticipant::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $byBatch = PpgParticipant::selectRaw('batch, count(*) as total')
            ->groupBy('batch')
            ->pluck('total', 'batch');

        return response()->json(['data' => [
            'total' => PpgParticipant::count(),
            'by_status' => $byStatus,
            'by_batch' => $byBatch,
        ]]);
    }
}
