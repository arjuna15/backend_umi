<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Grade;
use App\Models\Course;
use App\Models\Material;
use App\Models\Submission;
use Illuminate\Support\Facades\Storage;

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
            $grades = Grade::with([
                'course.materials',
                'course.assignments.submissions',
                'course.attendances.records',
                'course.forums.replies'
            ])->where('mahasiswa_id', $user->id)->get();
            $billings = \App\Models\Billing::where('user_id', $user->id)->get();
            return response()->json([
                'user' => $user,
                'krs' => $grades,
                'billings' => $billings
            ]);
        }

        if ($user->role === 'dosen') {
            $courses = Course::with([
                'grades.mahasiswa',
                'materials',
                'assignments.submissions',
                'attendances.records',
                'forums.replies'
            ])->where('dosen_id', $user->id)->get();
            return response()->json([
                'user' => $user,
                'jadwal' => $courses
            ]);
        }

        if (in_array($user->role, ['admin', 'kaprodi'])) {
            $courses = Course::with(['dosen'])->get();
            $users = User::all();
            return response()->json([
                'user' => $user,
                'courses' => $courses,
                'users_count' => $users->count(),
            ]);
        }

        return response()->json(['message' => 'Unauthorized role'], 403);
    }

    public function uploadMateri(Request $request, $courseId)
    {
        $request->validate([
            'title' => 'required',
            'file' => 'required|file',
        ]);

        $path = $request->file('file')->store('materials', 'public');

        $material = Material::create([
            'course_id' => $courseId,
            'title' => $request->title,
            'content_link' => '/storage/' . $path,
        ]);

        return response()->json(['message' => 'Material uploaded successfully', 'material' => $material]);
    }

    public function uploadSubmission(Request $request, $assignmentId)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        $user = $request->user();

        $path = $request->file('file')->store('submissions', 'public');

        $submission = Submission::create([
            'assignment_id' => $assignmentId,
            'mahasiswa_id' => $user->id,
            'file_path' => '/storage/' . $path,
            'grade' => null,
        ]);

        return response()->json(['message' => 'Submission uploaded successfully', 'submission' => $submission]);
    }

    public function createAssignment(Request $request, $courseId)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'deadline' => 'required|date',
        ]);
        
        $assignment = \App\Models\Assignment::create([
            'course_id' => $courseId,
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
        ]);
        
        return response()->json(['message' => 'Assignment created successfully', 'assignment' => $assignment]);
    }

    public function createAttendance(Request $request, $courseId)
    {
        $request->validate([
            'meeting_number' => 'required|numeric',
            'date' => 'required|date',
        ]);
        
        $attendance = \App\Models\Attendance::create([
            'course_id' => $courseId,
            'meeting_number' => $request->meeting_number,
            'date' => $request->date,
        ]);
        
        // Auto-create blank records for all students enrolled
        $grades = \App\Models\Grade::where('course_id', $courseId)->get();
        foreach($grades as $grade) {
            \App\Models\AttendanceRecord::create([
                'attendance_id' => $attendance->id,
                'mahasiswa_id' => $grade->mahasiswa_id,
                'status' => 'absent'
            ]);
        }
        
        return response()->json(['message' => 'Attendance session created']);
    }

    public function updateAttendanceRecord(Request $request, $attendanceId)
    {
        $request->validate([
            'mahasiswa_id' => 'required|numeric',
            'status' => 'required|in:present,absent'
        ]);
        
        $record = \App\Models\AttendanceRecord::where('attendance_id', $attendanceId)
            ->where('mahasiswa_id', $request->mahasiswa_id)
            ->first();
            
        if ($record) {
            $record->update(['status' => $request->status]);
        }
        
        return response()->json(['message' => 'Record updated']);
    }

    public function updateGrade(Request $request, $gradeId)
    {
        $request->validate([
            'score' => 'numeric|nullable',
            'grade' => 'string|nullable',
        ]);
        
        $grade = \App\Models\Grade::findOrFail($gradeId);
        $grade->update([
            'score' => $request->score,
            'grade' => $request->grade,
        ]);
        
        return response()->json(['message' => 'Grade updated', 'data' => $grade]);
    }

    // --- Forum Diskusi ---

    public function createForumThread(Request $request, $courseId)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        $forum = \App\Models\Forum::create([
            'course_id' => $courseId,
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return response()->json(['message' => 'Forum thread created', 'forum' => $forum]);
    }

    public function replyForum(Request $request, $forumId)
    {
        $request->validate([
            'content' => 'required',
        ]);

        $reply = \App\Models\ForumReply::create([
            'forum_id' => $forumId,
            'user_id' => $request->user()->id,
            'content' => $request->content,
        ]);

        return response()->json(['message' => 'Reply posted', 'reply' => $reply]);
    }

    // --- Admin CRUD ---

    public function getUsers()
    {
        return response()->json(User::all());
    }

    public function createUser(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'nim_nip' => 'required|unique:users',
            'role' => 'required|in:admin,superadmin,kaprodi,dosen,mahasiswa',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'nim_nip' => $request->nim_nip,
            'role' => $request->role,
            'prodi' => $request->prodi,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'User created', 'user' => $user]);
    }

    public function deleteUser($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['message' => 'User deleted']);
    }

    public function getCourses()
    {
        return response()->json(Course::with('dosen')->get());
    }

    public function createCourse(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'code' => 'required|unique:courses',
            'sks' => 'required|numeric',
            'dosen_id' => 'required|exists:users,id'
        ]);

        $course = Course::create([
            'name' => $request->name,
            'code' => $request->code,
            'sks' => $request->sks,
            'dosen_id' => $request->dosen_id,
        ]);

        return response()->json(['message' => 'Course created', 'course' => $course]);
    }

    public function deleteCourse($id)
    {
        Course::findOrFail($id)->delete();
        return response()->json(['message' => 'Course deleted']);
    }
}
