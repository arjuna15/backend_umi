<?php

namespace App\Http\Controllers\Siakad;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use App\Models\JobApplication;
use Illuminate\Http\Request;

class CareerController extends Controller
{
    public function jobs(Request $request)
    {
        $query = JobPosting::withCount('applications');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('company_name', 'like', "%{$s}%")
                  ->orWhere('position_title', 'like', "%{$s}%");
            });
        }

        $jobs = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

        return response()->json(['data' => $jobs]);
    }

    public function storeJob(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'position_title' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'employment_type' => 'required|in:full_time,part_time,internship,contract',
            'salary_range' => 'nullable|string|max:100',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'deadline' => 'nullable|date',
            'contact_email' => 'nullable|email|max:255',
            'status' => 'nullable|in:open,closed,draft',
        ]);

        $validated['posted_by'] = $request->user()->id;

        $job = JobPosting::create($validated);

        return response()->json(['data' => $job], 201);
    }

    public function updateJob($id, Request $request)
    {
        $job = JobPosting::findOrFail($id);

        $validated = $request->validate([
            'company_name' => 'sometimes|string|max:255',
            'position_title' => 'sometimes|string|max:255',
            'location' => 'nullable|string|max:255',
            'employment_type' => 'sometimes|in:full_time,part_time,internship,contract',
            'salary_range' => 'nullable|string|max:100',
            'description' => 'sometimes|string',
            'requirements' => 'nullable|string',
            'deadline' => 'nullable|date',
            'contact_email' => 'nullable|email|max:255',
            'status' => 'sometimes|in:open,closed,draft',
        ]);

        $job->update($validated);

        return response()->json(['data' => $job]);
    }

    public function deleteJob($id)
    {
        $job = JobPosting::findOrFail($id);
        $job->delete();

        return response()->json(['message' => 'Job posting deleted']);
    }

    public function applications($jobId)
    {
        $job = JobPosting::findOrFail($jobId);

        $applications = JobApplication::with('user')
            ->where('job_posting_id', $jobId)
            ->orderBy('applied_at', 'desc')
            ->get();

        return response()->json(['data' => $applications]);
    }

    public function apply($jobId, Request $request)
    {
        $job = JobPosting::findOrFail($jobId);

        $validated = $request->validate([
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'cover_letter' => 'nullable|string',
        ]);

        $data = [
            'job_posting_id' => $job->id,
            'user_id' => $request->user()->id,
            'cover_letter' => $validated['cover_letter'] ?? null,
            'status' => 'pending',
            'applied_at' => now(),
        ];

        if ($request->hasFile('resume')) {
            $data['resume_path'] = $request->file('resume')->store('resumes', 'public');
        }

        $application = JobApplication::create($data);

        return response()->json(['data' => $application], 201);
    }

    public function updateApplicationStatus($id, Request $request)
    {
        $application = JobApplication::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,reviewed,shortlisted,rejected,accepted',
        ]);

        $application->update($validated);

        return response()->json(['data' => $application]);
    }

    public function stats()
    {
        $totalJobs = JobPosting::count();
        $openJobs = JobPosting::where('status', 'open')->count();
        $totalApplications = JobApplication::count();
        $placements = JobApplication::where('status', 'accepted')->count();

        return response()->json(['data' => [
            'total_jobs' => $totalJobs,
            'open_jobs' => $openJobs,
            'total_applications' => $totalApplications,
            'placements' => $placements,
        ]]);
    }
}
