<?php

namespace App\Http\Controllers\Siakad;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use App\Models\StudentScholarship;
use App\Models\User;
use Illuminate\Http\Request;

class ScholarshipController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentScholarship::with(['user', 'scholarship']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $list = $query->orderBy('created_at', 'desc')->get();
        return response()->json(['data' => $list]);
    }

    public function masters()
    {
        $list = Scholarship::all();
        return response()->json(['data' => $list]);
    }

    public function storeMaster(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'provider' => 'required|string|max:255',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
        ]);

        $master = Scholarship::create($request->all());
        return response()->json(['message' => 'Master beasiswa berhasil ditambahkan.', 'data' => $master], 201);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nim' => 'required|string',
            'scholarship_id' => 'required|exists:scholarships,id',
            'start_semester' => 'required|string',
            'sk_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $student = User::where('nim', $request->nim)->first();
        if (!$student) {
            return response()->json(['message' => 'Mahasiswa dengan NIM tersebut tidak ditemukan.'], 404);
        }

        // Simpan beasiswa mahasiswa
        $studentScholarship = StudentScholarship::create([
            'user_id' => $student->id,
            'scholarship_id' => $request->scholarship_id,
            'start_semester' => $request->start_semester,
            'sk_number' => $request->sk_number,
            'notes' => $request->notes,
            'status' => 'active'
        ]);

        return response()->json(['message' => 'Penerima beasiswa berhasil ditambahkan.', 'data' => $studentScholarship], 201);
    }

    public function updateStatus($id, Request $request)
    {
        $request->validate([
            'status' => 'required|in:active,revoked,completed',
            'end_semester' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $item = StudentScholarship::findOrFail($id);
        $item->update([
            'status' => $request->status,
            'end_semester' => $request->status !== 'active' ? $request->end_semester : null,
            'notes' => $request->notes ?? $item->notes
        ]);

        return response()->json(['message' => 'Status beasiswa mahasiswa berhasil diperbarui.', 'data' => $item]);
    }

    public function stats()
    {
        $totalActive = StudentScholarship::where('status', 'active')->count();
        $totalRevoked = StudentScholarship::where('status', 'revoked')->count();
        $totalCompleted = StudentScholarship::where('status', 'completed')->count();
        
        $byScholarship = StudentScholarship::selectRaw('scholarships.name, count(*) as count')
            ->join('scholarships', 'student_scholarships.scholarship_id', '=', 'scholarships.id')
            ->groupBy('scholarships.name')
            ->get();

        return response()->json([
            'stats' => [
                'active' => $totalActive,
                'revoked' => $totalRevoked,
                'completed' => $totalCompleted,
                'total' => $totalActive + $totalRevoked + $totalCompleted
            ],
            'distribution' => $byScholarship
        ]);
    }
}
