<?php

namespace App\Http\Controllers\Siakad;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $courses = Course::with(['dosen'])->get();
        $users = User::all();

        // Compute prodi distribution dynamically from users table where role = 'mahasiswa'
        $dbDistribution = User::where('role', 'mahasiswa')
            ->whereNotNull('prodi')
            ->where('prodi', '!=', '')
            ->groupBy('prodi')
            ->select('prodi as name', DB::raw('count(*) as users'))
            ->get()
            ->toArray();

        if (empty($dbDistribution)) {
            $prodiDistribution = [
                ['name' => 'Sistem Informasi', 'users' => 120],
                ['name' => 'Teknik Informatika', 'users' => 180],
                ['name' => 'Manajemen', 'users' => 200],
                ['name' => 'Akuntansi', 'users' => 150]
            ];
        } else {
            $prodiDistribution = array_map(function ($item) {
                return [
                    'name' => $item['name'],
                    'users' => (int) $item['users']
                ];
            }, $dbDistribution);
        }

        return response()->json([
            'user' => $user,
            'courses' => $courses,
            'users_count' => $users->count(),
            'prodi_distribution' => $prodiDistribution,
        ]);
    }
}
