<?php
namespace App\Http\Controllers\Siakad;

use App\Http\Controllers\Controller;
use App\Models\SpmiDocument;
use App\Models\User;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QualityAssuranceController extends Controller
{
    public function getSpmiDocs()
    {
        $docs = SpmiDocument::orderBy('created_at', 'desc')->get();
        return response()->json(['data' => $docs]);
    }

    public function uploadSpmiDoc(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:standar,audit,evaluasi,akreditasi',
            'academic_year' => 'required|string',
        ]);
        $path = null;
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('spmi', 'public');
        }
        $doc = SpmiDocument::create([
            'title' => $request->title,
            'category' => $request->category,
            'file_path' => $path ?? '',
            'academic_year' => $request->academic_year,
        ]);
        return response()->json(['message' => 'Dokumen SPMI berhasil diunggah.', 'data' => $doc], 201);
    }

    public function getSpmeDocs()
    {
        $docs = \App\Models\SpmeDocument::orderBy('created_at', 'desc')->get();
        return response()->json(['data' => $docs]);
    }

    public function uploadSpmeDoc(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'status' => 'nullable|string',
            'year' => 'required|integer',
            'upload_date' => 'nullable|date',
            'file' => 'nullable|file',
        ]);

        $path = null;
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('spme', 'public');
        }

        $doc = \App\Models\SpmeDocument::create([
            'name' => $request->name,
            'category' => $request->category,
            'status' => $request->status ?? 'pending',
            'year' => (int) $request->year,
            'upload_date' => $request->upload_date ?? now()->toDateString(),
            'file_path' => $path,
        ]);

        return response()->json(['message' => 'Dokumen SPME berhasil diunggah.', 'data' => $doc], 201);
    }

    public function getSurveyStats()
    {
        $surveys = \App\Models\ServiceSurvey::all();
        
        $grouped = [];
        
        foreach ($surveys as $survey) {
            $cat = $survey->category;
            
            // Map database categories to frontend categories where needed
            if ($cat === 'sarpras') {
                $frontendCat = 'sarana';
            } elseif ($cat === 'keuangan') {
                $frontendCat = 'administrasi';
            } else {
                $frontendCat = $cat;
            }
            
            if (!isset($grouped[$frontendCat])) {
                $grouped[$frontendCat] = [
                    'label' => ucfirst($frontendCat),
                    'items' => []
                ];
            }
            
            $grouped[$frontendCat]['items'][] = [
                'aspek' => $survey->aspect,
                'aspect' => $survey->aspect,
                'rating' => (float) $survey->rating,
                'responden' => $survey->respondents_count,
                'respondents_count' => $survey->respondents_count,
            ];
        }
        
        // Also support database keys directly
        foreach ($surveys as $survey) {
            $cat = $survey->category;
            if (!isset($grouped[$cat])) {
                $grouped[$cat] = [
                    'label' => ucfirst($cat),
                    'items' => []
                ];
            }
            // Avoid duplicating items if frontendCat is the same as cat
            if ($cat === 'sarpras' || $cat === 'keuangan') {
                $grouped[$cat]['items'][] = [
                    'aspek' => $survey->aspect,
                    'aspect' => $survey->aspect,
                    'rating' => (float) $survey->rating,
                    'responden' => $survey->respondents_count,
                    'respondents_count' => $survey->respondents_count,
                ];
            }
        }

        return response()->json(['data' => $grouped]);
    }

    public function getIkuStats()
    {
        $totalMhs = User::where('role', 'mahasiswa')->count();
        $totalDosen = User::where('role', 'dosen')->count();
        $totalAlumni = DB::table('alumni')->count() ?: $totalMhs;

        return response()->json(['data' => [
            ['id' => 1, 'name' => 'Lulusan Mendapat Pekerjaan yang Layak', 'target' => 70, 'actual' => 65, 'unit' => '%'],
            ['id' => 2, 'name' => 'Mahasiswa Mendapat Pengalaman di Luar Kampus', 'target' => 50, 'actual' => 38, 'unit' => '%'],
            ['id' => 3, 'name' => 'Dosen Berkegiatan di Luar Kampus', 'target' => 50, 'actual' => 42, 'unit' => '%'],
            ['id' => 4, 'name' => 'Praktisi Mengajar di Dalam Kampus', 'target' => 30, 'actual' => 22, 'unit' => '%'],
            ['id' => 5, 'name' => 'Hasil Kerja Dosen Digunakan Masyarakat', 'target' => 20, 'actual' => 15, 'unit' => '%'],
            ['id' => 6, 'name' => 'Program Studi Bekerjasama dengan Mitra', 'target' => 80, 'actual' => 72, 'unit' => '%'],
            ['id' => 7, 'name' => 'Kelas yang Kolaboratif dan Partisipatif', 'target' => 60, 'actual' => 55, 'unit' => '%'],
            ['id' => 8, 'name' => 'Program Studi Berstandar Internasional', 'target' => 10, 'actual' => 5, 'unit' => '%'],
        ], 'summary' => [
            'total_mahasiswa' => $totalMhs,
            'total_dosen' => $totalDosen,
            'total_alumni' => $totalAlumni,
        ]]);
    }
}
