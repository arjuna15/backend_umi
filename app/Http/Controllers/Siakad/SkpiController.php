<?php
namespace App\Http\Controllers\Siakad;

use App\Http\Controllers\Controller;
use App\Models\Skpi;
use App\Models\Prestasi;
use Illuminate\Http\Request;

class SkpiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'admin' || $user->role === 'superadmin') {
            $items = Prestasi::with('user')->orderBy('created_at', 'desc')->get();
            $skpis = Skpi::with(['user', 'prestasis'])->orderBy('created_at', 'desc')->get();
        } else {
            $items = Prestasi::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
            $skpis = Skpi::where('user_id', $user->id)->with('prestasis')->orderBy('created_at', 'desc')->get();
        }
        return response()->json(['prestasis' => $items, 'skpis' => $skpis]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:akademik,non-akademik',
            'level' => 'required|in:internal,regional,nasional,internasional',
        ]);

        $path = null;
        if ($request->hasFile('certificate')) {
            $path = $request->file('certificate')->store('prestasi', 'public');
        }

        $prestasi = Prestasi::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'category' => $request->category,
            'level' => $request->level,
            'certificate_path' => $path,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Prestasi berhasil diajukan.', 'data' => $prestasi], 201);
    }

    public function approve($id, Request $request)
    {
        $request->validate(['status' => 'required|in:approved,rejected']);
        $prestasi = Prestasi::findOrFail($id);
        $prestasi->update(['status' => $request->status]);
        return response()->json(['message' => 'Status prestasi diperbarui.', 'data' => $prestasi]);
    }

    public function submitSkpi(Request $request)
    {
        $skpi = Skpi::create([
            'user_id' => $request->user()->id,
            'status' => 'pending',
        ]);
        return response()->json(['message' => 'Pengajuan SKPI berhasil.', 'data' => $skpi], 201);
    }

    public function approveSkpi($id, Request $request)
    {
        $request->validate(['status' => 'required|in:approved,rejected', 'notes' => 'nullable|string']);
        $skpi = Skpi::findOrFail($id);
        $skpi->update([
            'status' => $request->status,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'notes' => $request->notes,
        ]);
        return response()->json(['message' => 'Status SKPI diperbarui.', 'data' => $skpi]);
    }
}
