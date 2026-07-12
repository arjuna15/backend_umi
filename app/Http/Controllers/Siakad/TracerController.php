<?php

namespace App\Http\Controllers\Siakad;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\TracerSurvey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TracerController extends Controller
{
    /**
     * List alumni with survey status.
     */
    public function index(): JsonResponse
    {
        $alumni = Alumni::with(['user:id,name,nim_nip', 'surveys' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->orderByDesc('graduation_year')
            ->paginate(20);

        return response()->json($alumni);
    }

    /**
     * Create alumni record.
     */
    public function storeAlumni(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'graduation_year' => 'required|integer|min:2000|max:2099',
            'program_studi' => 'required|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
        ]);

        $alumni = Alumni::create($validated);
        $alumni->load('user:id,name,nim_nip');

        return response()->json($alumni, 201);
    }

    /**
     * Submit tracer survey.
     */
    public function storeSurvey(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'alumni_id' => 'required|exists:alumni,id',
            'questionnaire' => 'required|array',
            'salary_range' => 'nullable|string|max:100',
            'satisfaction' => 'required|in:sangat_puas,puas,cukup,kurang',
        ]);

        $validated['submitted_at'] = now();

        $survey = TracerSurvey::create($validated);
        $survey->load('alumni.user:id,name');

        return response()->json($survey, 201);
    }

    /**
     * Return aggregated stats.
     */
    public function stats(): JsonResponse
    {
        $byYear = Alumni::selectRaw('graduation_year, COUNT(*) as total')
            ->groupBy('graduation_year')
            ->orderByDesc('graduation_year')
            ->get();

        $satisfactionDist = TracerSurvey::selectRaw('satisfaction, COUNT(*) as total')
            ->groupBy('satisfaction')
            ->get();

        $salaryRanges = TracerSurvey::selectRaw('salary_range, COUNT(*) as total')
            ->whereNotNull('salary_range')
            ->groupBy('salary_range')
            ->get();

        $totalAlumni = Alumni::count();
        $totalSurveys = TracerSurvey::count();

        return response()->json([
            'total_alumni' => $totalAlumni,
            'total_surveys' => $totalSurveys,
            'by_graduation_year' => $byYear,
            'satisfaction_distribution' => $satisfactionDist,
            'salary_ranges' => $salaryRanges,
        ]);
    }

    /**
     * Export alumni + survey data as CSV download.
     */
    public function exportCsv(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="tracer_study_export.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Nama', 'NIM', 'Program Studi', 'Tahun Lulus',
                'Telepon', 'Email Kontak', 'Perusahaan', 'Jabatan',
                'Kepuasan', 'Rentang Gaji', 'Tanggal Survey',
            ]);

            Alumni::with(['user:id,name,nim_nip', 'surveys'])->chunk(100, function ($alumniChunk) use ($handle) {
                foreach ($alumniChunk as $alumni) {
                    $survey = $alumni->surveys->first();
                    fputcsv($handle, [
                        $alumni->user?->name,
                        $alumni->user?->nim_nip,
                        $alumni->program_studi,
                        $alumni->graduation_year,
                        $alumni->contact_phone,
                        $alumni->contact_email,
                        $alumni->company,
                        $alumni->position,
                        $survey?->satisfaction,
                        $survey?->salary_range,
                        $survey?->submitted_at,
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }
}
