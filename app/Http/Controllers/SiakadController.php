<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Grade;
use App\Models\Course;

class SiakadController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'nim_nip' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('nim_nip', $request->nim_nip)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'NIM/NIP atau Password salah.'
            ], 401);
        }

        $token = $user->createToken('siakad-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'nim_nip' => $user->nim_nip,
                'role' => $user->role,
                'prodi' => $user->prodi,
            ]
        ]);
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'mahasiswa') {
            $grades = Grade::with('course')->where('mahasiswa_id', $user->id)->get();
            return response()->json([
                'user' => $user,
                'krs' => $grades
            ]);
        } 
        
        if ($user->role === 'dosen') {
            $courses = Course::with(['grades.mahasiswa'])->where('dosen_id', $user->id)->get();
            return response()->json([
                'user' => $user,
                'jadwal' => $courses
            ]);
        }

        return response()->json(['message' => 'Unauthorized role'], 403);
    }
}
