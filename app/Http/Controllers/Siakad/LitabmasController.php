<?php
namespace App\Http\Controllers\Siakad;

use App\Http\Controllers\Controller;
use App\Models\LitabmasProposal;
use Illuminate\Http\Request;

class LitabmasController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'admin' || $user->role === 'superadmin') {
            $list = LitabmasProposal::with('user')->orderBy('created_at', 'desc')->get();
        } else {
            $list = LitabmasProposal::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        }
        return response()->json(['data' => $list]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:penelitian,pengabdian',
            'title' => 'required|string|max:255',
            'abstract' => 'required|string',
            'budget' => 'required|numeric|min:0',
        ]);
        $path = null;
        if ($request->hasFile('proposal_file')) {
            $path = $request->file('proposal_file')->store('litabmas', 'public');
        }
        $proposal = LitabmasProposal::create([
            'user_id' => $request->user()->id,
            'type' => $request->type,
            'title' => $request->title,
            'abstract' => $request->abstract,
            'budget' => $request->budget,
            'proposal_file' => $path,
            'status' => $request->status ?? 'pending',
        ]);
        return response()->json(['message' => 'Proposal berhasil diajukan.', 'data' => $proposal], 201);
    }

    public function review($id, Request $request)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'reviewer_notes' => 'nullable|string',
        ]);
        $proposal = LitabmasProposal::findOrFail($id);
        $proposal->update($request->only('status', 'reviewer_notes'));
        return response()->json(['message' => 'Proposal diperbarui.', 'data' => $proposal]);
    }
}
