<?php

namespace App\Http\Controllers\Siakad;

use App\Http\Controllers\Controller;
use App\Models\PmbApplicant;
use App\Models\PmbDocument;
use App\Models\PmbPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PmbController extends Controller
{
    /**
     * List all PMB periods.
     */
    public function periods(): JsonResponse
    {
        $periods = PmbPeriod::withCount('applicants')->orderByDesc('created_at')->get();

        return response()->json($periods);
    }

    /**
     * Create new PMB period (admin).
     */
    public function createPeriod(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'academic_year' => 'required|string|max:20',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'in:draft,open,closed',
            'quota' => 'integer|min:1',
        ]);

        $period = PmbPeriod::create($validated);

        return response()->json($period, 201);
    }

    /**
     * Update a PMB period.
     */
    public function updatePeriod($id, Request $request): JsonResponse
    {
        $period = PmbPeriod::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'academic_year' => 'sometimes|string|max:20',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date',
            'status' => 'in:draft,open,closed',
            'quota' => 'integer|min:1',
        ]);

        $period->update($validated);

        return response()->json($period);
    }

    /**
     * Public/student applies to a period, auto-generate registration_number.
     */
    public function apply($periodId, Request $request): JsonResponse
    {
        $period = PmbPeriod::findOrFail($periodId);

        if ($period->status !== 'open') {
            return response()->json(['message' => 'Periode pendaftaran belum/sudah ditutup.'], 422);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:L,P',
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'school_origin' => 'nullable|string|max:255',
            'program_choice' => 'required|string|max:255',
        ]);

        // Auto-generate registration number: PMB-{year}-{sequence}
        $lastApplicant = PmbApplicant::where('pmb_period_id', $periodId)
            ->orderByDesc('id')
            ->first();
        $sequence = $lastApplicant ? ((int) substr($lastApplicant->registration_number, -5)) + 1 : 1;
        $registrationNumber = 'PMB-' . $period->academic_year . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);

        $applicant = PmbApplicant::create(array_merge($validated, [
            'pmb_period_id' => $period->id,
            'registration_number' => $registrationNumber,
            'status' => 'pending',
        ]));

        return response()->json($applicant, 201);
    }

    /**
     * Upload document file for an applicant.
     */
    public function uploadDocument($applicantId, Request $request): JsonResponse
    {
        $applicant = PmbApplicant::findOrFail($applicantId);

        $request->validate([
            'type' => 'required|string|max:50',
            'file' => 'required|file|max:5120', // max 5MB
        ]);

        $file = $request->file('file');
        $path = $file->store('pmb-documents/' . $applicantId, 'public');

        $document = PmbDocument::create([
            'pmb_applicant_id' => $applicant->id,
            'type' => $request->type,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'uploaded_at' => now(),
        ]);

        return response()->json($document, 201);
    }

    /**
     * List applicants for a period.
     */
    public function getApplicants($periodId): JsonResponse
    {
        $applicants = PmbApplicant::where('pmb_period_id', $periodId)
            ->withCount('documents')
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json($applicants);
    }

    /**
     * Admin updates applicant status.
     */
    public function updateStatus($applicantId, Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,verified,accepted,rejected,enrolled',
            'notes' => 'nullable|string',
        ]);

        $applicant = PmbApplicant::findOrFail($applicantId);
        $applicant->update([
            'status' => $request->status,
            'notes' => $request->notes ?? $applicant->notes,
        ]);

        return response()->json($applicant);
    }

    /**
     * Get applicant detail with documents.
     */
    public function getApplicantDetail($applicantId): JsonResponse
    {
        $applicant = PmbApplicant::with(['documents', 'period'])
            ->findOrFail($applicantId);

        return response()->json($applicant);
    }

    /**
     * Get PMB statistics dashboard.
     */
    public function dashboard(): JsonResponse
    {
        $totalApplicants = PmbApplicant::count();

        $byStatus = PmbApplicant::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $byProgram = PmbApplicant::selectRaw('program_choice, COUNT(*) as total')
            ->groupBy('program_choice')
            ->get();

        $activePeriods = PmbPeriod::where('status', 'open')
            ->withCount('applicants')
            ->get();

        return response()->json([
            'total_applicants' => $totalApplicants,
            'by_status' => $byStatus,
            'by_program' => $byProgram,
            'active_periods' => $activePeriods,
        ]);
    }
}
