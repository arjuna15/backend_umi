<?php
namespace App\Http\Controllers\Siakad;

use App\Http\Controllers\Controller;
use App\Models\YudisiumRegistration;
use App\Models\WisudaRegistration;
use Illuminate\Http\Request;

class GraduationController extends Controller
{
    public function getYudisiumList(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'admin' || $user->role === 'superadmin') {
            $list = YudisiumRegistration::with(['user', 'wisuda'])->orderBy('created_at', 'desc')->get();
        } else {
            $list = YudisiumRegistration::where('user_id', $user->id)->with('wisuda')->orderBy('created_at', 'desc')->get();
        }
        return response()->json(['data' => $list]);
    }

    public function applyYudisium(Request $request)
    {
        $request->validate([
            'thesis_title' => 'required|string',
            'gpa' => 'required|numeric|min:0|max:4',
        ]);
        $path = null;
        if ($request->hasFile('thesis_file')) {
            $path = $request->file('thesis_file')->store('thesis', 'public');
        }
        $reg = YudisiumRegistration::create([
            'user_id' => $request->user()->id,
            'thesis_title' => $request->thesis_title,
            'gpa' => $request->gpa,
            'thesis_file' => $path,
            'status' => 'pending',
        ]);
        return response()->json(['message' => 'Pengajuan yudisium berhasil.', 'data' => $reg], 201);
    }

    public function verifyYudisium($id, Request $request)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
            'is_free_billing' => 'nullable|boolean',
            'is_free_library' => 'nullable|boolean',
        ]);
        $reg = YudisiumRegistration::findOrFail($id);
        $reg->update($request->only('status', 'is_free_billing', 'is_free_library'));
        return response()->json(['message' => 'Status yudisium diperbarui.', 'data' => $reg]);
    }

    public function getWisudaList(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'admin' || $user->role === 'superadmin') {
            $list = WisudaRegistration::with('yudisium.user')->orderBy('created_at', 'desc')->get();
        } else {
            $yudIds = YudisiumRegistration::where('user_id', $user->id)->pluck('id');
            $list = WisudaRegistration::whereIn('yudisium_registration_id', $yudIds)->with('yudisium')->get();
        }
        return response()->json(['data' => $list]);
    }

    public function applyWisuda(Request $request)
    {
        $request->validate([
            'yudisium_registration_id' => 'required|exists:yudisium_registrations,id',
            'toga_size' => 'required|in:S,M,L,XL,XXL',
        ]);
        $wisuda = WisudaRegistration::create([
            'yudisium_registration_id' => $request->yudisium_registration_id,
            'toga_size' => $request->toga_size,
            'status' => 'pending',
        ]);
        return response()->json(['message' => 'Pendaftaran wisuda berhasil.', 'data' => $wisuda], 201);
    }

    public function confirmWisuda($id, Request $request)
    {
        $request->validate(['seat_number' => 'nullable|string']);
        $wisuda = WisudaRegistration::findOrFail($id);
        $wisuda->update(['status' => 'confirmed', 'seat_number' => $request->seat_number]);
        return response()->json(['message' => 'Wisuda dikonfirmasi.', 'data' => $wisuda]);
    }
}
