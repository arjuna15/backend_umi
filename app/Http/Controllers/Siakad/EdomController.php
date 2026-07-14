<?php
namespace App\Http\Controllers\Siakad;

use App\Http\Controllers\Controller;
use App\Models\EdomQuestion;
use App\Models\EdomAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EdomController extends Controller
{
    public function getQuestions()
    {
        $questions = EdomQuestion::orderBy('id')->get();
        if ($questions->isEmpty()) {
            $defaults = [
                ['question' => 'Dosen menguasai materi perkuliahan dengan baik', 'category' => 'Kompetensi'],
                ['question' => 'Dosen menyampaikan materi dengan jelas dan mudah dipahami', 'category' => 'Pedagogik'],
                ['question' => 'Dosen memberikan umpan balik yang konstruktif', 'category' => 'Pedagogik'],
                ['question' => 'Dosen hadir tepat waktu dan disiplin', 'category' => 'Profesionalisme'],
                ['question' => 'Dosen bersikap ramah dan terbuka terhadap mahasiswa', 'category' => 'Kepribadian'],
            ];
            foreach ($defaults as $d) { EdomQuestion::create($d); }
            $questions = EdomQuestion::orderBy('id')->get();
        }
        return response()->json(['data' => $questions]);
    }

    public function submitAnswers(Request $request)
    {
        $request->validate([
            'course_id' => 'required|integer',
            'dosen_id' => 'required|integer',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:edom_questions,id',
            'answers.*.score' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string',
        ]);

        $existing = EdomAnswer::where('user_id', $request->user()->id)
            ->where('course_id', $request->course_id)
            ->where('dosen_id', $request->dosen_id)
            ->exists();
        if ($existing) {
            return response()->json(['message' => 'Anda sudah mengisi evaluasi untuk dosen ini.'], 422);
        }

        $totalScore = 0;
        foreach ($request->answers as $a) {
            EdomAnswer::create([
                'question_id' => $a['question_id'],
                'user_id' => $request->user()->id,
                'course_id' => $request->course_id,
                'dosen_id' => $request->dosen_id,
                'score' => $a['score'],
                'comments' => $request->comments,
            ]);
            $totalScore += $a['score'];
        }

        $avgScore = count($request->answers) > 0 ? $totalScore / count($request->answers) : 5;

        \App\Models\Edom::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'mahasiswa_id' => $request->user()->id,
                'dosen_id' => $request->dosen_id,
                'course_id' => $request->course_id,
            ],
            [
                'score' => round($avgScore, 1),
                'comment' => $request->comments ?? 'Cukup baik.'
            ]
        );

        return response()->json(['message' => 'Evaluasi dosen berhasil disimpan.']);
    }

    public function getDosenStats($dosenId)
    {
        $stats = EdomAnswer::where('dosen_id', $dosenId)
            ->select('question_id', DB::raw('AVG(score) as avg_score'), DB::raw('COUNT(*) as total'))
            ->groupBy('question_id')
            ->with('question')
            ->get();
        $overall = EdomAnswer::where('dosen_id', $dosenId)->avg('score');
        return response()->json(['data' => $stats, 'overall' => round($overall, 2)]);
    }
}
