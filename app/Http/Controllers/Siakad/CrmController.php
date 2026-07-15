<?php

namespace App\Http\Controllers\Siakad;

use App\Http\Controllers\Controller;
use App\Models\CamabaProspect;
use App\Models\CamabaFollowup;
use Illuminate\Http\Request;

class CrmController extends Controller
{
    public function index(Request $request)
    {
        $query = CamabaProspect::with('handler');

        if ($request->has('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('source')) {
            $query->where('source', $request->source);
        }

        $prospects = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

        return response()->json(['data' => $prospects]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'school_origin' => 'nullable|string|max:255',
            'program_interest' => 'nullable|string|max:255',
            'source' => 'required|in:website,instagram,whatsapp,pameran,referral,lainnya',
            'status' => 'nullable|in:new,contacted,interested,registered,lost',
            'follow_up_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $validated['handled_by'] = $request->user()->id;

        $prospect = CamabaProspect::create($validated);

        return response()->json(['data' => $prospect], 201);
    }

    public function update($id, Request $request)
    {
        $prospect = CamabaProspect::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'sometimes|string|max:20',
            'school_origin' => 'nullable|string|max:255',
            'program_interest' => 'nullable|string|max:255',
            'source' => 'sometimes|in:website,instagram,whatsapp,pameran,referral,lainnya',
            'status' => 'sometimes|in:new,contacted,interested,registered,lost',
            'follow_up_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'handled_by' => 'nullable|exists:users,id',
        ]);

        $prospect->update($validated);

        return response()->json(['data' => $prospect]);
    }

    public function destroy($id)
    {
        $prospect = CamabaProspect::findOrFail($id);
        $prospect->delete();

        return response()->json(['message' => 'Prospect deleted']);
    }

    public function addFollowup($id, Request $request)
    {
        $prospect = CamabaProspect::findOrFail($id);

        $validated = $request->validate([
            'method' => 'required|in:whatsapp,phone,email,visit',
            'notes' => 'required|string',
        ]);

        $followup = CamabaFollowup::create([
            'prospect_id' => $prospect->id,
            'user_id' => $request->user()->id,
            'method' => $validated['method'],
            'notes' => $validated['notes'],
            'followed_at' => now(),
        ]);

        return response()->json(['data' => $followup], 201);
    }

    public function getFollowups($id)
    {
        $prospect = CamabaProspect::findOrFail($id);

        $followups = CamabaFollowup::with('user')
            ->where('prospect_id', $id)
            ->orderBy('followed_at', 'desc')
            ->get();

        return response()->json(['data' => $followups]);
    }

    public function stats()
    {
        $byStatus = CamabaProspect::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $bySource = CamabaProspect::selectRaw('source, count(*) as total')
            ->groupBy('source')
            ->pluck('total', 'source');

        $total = CamabaProspect::count();
        $registered = CamabaProspect::where('status', 'registered')->count();
        $conversionRate = $total > 0 ? round(($registered / $total) * 100, 2) : 0;

        return response()->json(['data' => [
            'by_status' => $byStatus,
            'by_source' => $bySource,
            'total' => $total,
            'conversion_rate' => $conversionRate,
        ]]);
    }
}
