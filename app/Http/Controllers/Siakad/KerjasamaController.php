<?php

namespace App\Http\Controllers\Siakad;

use App\Http\Controllers\Controller;
use App\Models\Partnership;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KerjasamaController extends Controller
{
    public function index(Request $request)
    {
        $query = Partnership::with('creator');

        if ($request->has('search')) {
            $query->where('partner_name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('partner_type')) {
            $query->where('partner_type', $request->partner_type);
        }

        $partnerships = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

        return response()->json(['data' => $partnerships]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'partner_name' => 'required|string|max:255',
            'partner_type' => 'required|in:industri,pemerintah,universitas,ngo,lainnya',
            'mou_number' => 'nullable|string|unique:partnerships,mou_number',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'scope' => 'required|string',
            'status' => 'nullable|in:active,expired,draft',
            'pic_name' => 'nullable|string|max:255',
            'pic_phone' => 'nullable|string|max:20',
            'document' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        if ($request->hasFile('document')) {
            $validated['document_path'] = $request->file('document')->store('partnerships', 'public');
        }
        unset($validated['document']);

        $validated['created_by'] = $request->user()->id;

        $partnership = Partnership::create($validated);

        return response()->json(['data' => $partnership], 201);
    }

    public function update($id, Request $request)
    {
        $partnership = Partnership::findOrFail($id);

        $validated = $request->validate([
            'partner_name' => 'sometimes|string|max:255',
            'partner_type' => 'sometimes|in:industri,pemerintah,universitas,ngo,lainnya',
            'mou_number' => 'nullable|string|unique:partnerships,mou_number,' . $id,
            'start_date' => 'sometimes|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'scope' => 'sometimes|string',
            'status' => 'sometimes|in:active,expired,draft',
            'pic_name' => 'nullable|string|max:255',
            'pic_phone' => 'nullable|string|max:20',
            'document' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        if ($request->hasFile('document')) {
            $validated['document_path'] = $request->file('document')->store('partnerships', 'public');
        }
        unset($validated['document']);

        $partnership->update($validated);

        return response()->json(['data' => $partnership]);
    }

    public function destroy($id)
    {
        $partnership = Partnership::findOrFail($id);
        $partnership->delete();

        return response()->json(['message' => 'Partnership deleted']);
    }

    public function stats()
    {
        $totalActive = Partnership::where('status', 'active')->count();
        $expiringSoon = Partnership::where('status', 'active')
            ->whereNotNull('end_date')
            ->where('end_date', '<=', Carbon::now()->addMonths(3))
            ->count();
        $byType = Partnership::selectRaw('partner_type, count(*) as total')
            ->groupBy('partner_type')
            ->pluck('total', 'partner_type');

        return response()->json(['data' => [
            'total_active' => $totalActive,
            'expiring_soon' => $expiringSoon,
            'by_type' => $byType,
        ]]);
    }
}
