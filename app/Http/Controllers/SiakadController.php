<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Grade;
use App\Models\Course;
use App\Models\ConsultationMessage;
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
        $user->loadMissing('dosenWali');
        $portal = strtolower((string) $request->header('X-SIAKAD-PORTAL', ''));
        $isDosenPortal = $user->role === 'dosen' || ($user->role === 'kaprodi' && $portal === 'dosen');

        if ($user->role === 'mahasiswa') {
            $grades = Grade::with([
                'course.materials',
                'course.assignments.submissions',
                'course.attendances.records',
                'course.forums.replies',
                'course.quizzes.questions'
            ])->where('mahasiswa_id', $user->id)->get();
            $billings = \App\Models\Billing::where('user_id', $user->id)->get();
            $consultations = $this->formatConsultationMessages($user);
            return response()->json([
                'user' => $user,
                'krs' => $grades,
                'billings' => $billings,
                'consultations' => $consultations,
                'messages' => $consultations,
                'advisor' => $user->dosenWali ? [
                    'id' => $user->dosenWali->id,
                    'name' => $user->dosenWali->name,
                    'nidn' => $user->dosenWali->nim_nip,
                    'prodi' => $user->dosenWali->prodi,
                ] : null,
            ]);
        }

        if ($isDosenPortal) {
            $courses = Course::with([
                'grades.mahasiswa',
                'materials',
                'assignments.submissions',
                'attendances.records',
                'forums.replies',
                'quizzes.questions'
            ])->where('dosen_id', $user->id)->get();
            return response()->json([
                'user' => $user,
                'jadwal' => $courses,
                'courses' => $courses,
            ]);
        }

        if (in_array($user->role, ['admin', 'kaprodi', 'superadmin'])) {
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

    private function formatConsultationMessages(User $user)
    {
        if (!$user->dosenWali) {
            return collect();
        }

        return ConsultationMessage::query()
            ->where('mahasiswa_id', $user->id)
            ->where(function ($query) use ($user) {
                $query->where('dosen_id', $user->dosenWali->id)
                    ->orWhereNull('dosen_id');
            })
            ->orderBy('created_at')
            ->get()
            ->map(function (ConsultationMessage $message) use ($user) {
                $sender = $message->sender_role === 'mahasiswa' ? 'mahasiswa' : 'dosen';
                return [
                    'id' => $message->id,
                    'sender' => $sender,
                    'content' => $message->content,
                    'text' => $message->content,
                    'created_at' => $message->created_at?->toDateTimeString(),
                    'time' => $message->created_at?->format('j M Y, H:i'),
                    'is_from_student' => $sender === 'mahasiswa',
                    'advisor_name' => $user->dosenWali?->name,
                ];
            });
    }

    public function uploadMateri(Request $request, $courseId)
    {
        $request->validate([
            'title' => 'required',
            'file' => 'required|file',
            'session_num' => 'nullable|integer',
        ]);

        $path = $request->file('file')->store('materials', 'public');

        $material = Material::create([
            'course_id' => $courseId,
            'title' => $request->title,
            'session_num' => $request->session_num ?? 1,
            'type' => 'file',
            'content_link' => '/storage/' . $path,
        ]);

        return response()->json(['message' => 'Material uploaded successfully', 'material' => $material]);
    }

    public function saveMeetLink(Request $request, $courseId)
    {
        $request->validate([
            'session_num' => 'required|integer',
            'meet_url' => 'required|url',
        ]);

        $material = Material::updateOrCreate(
            [
                'course_id' => $courseId,
                'session_num' => $request->session_num,
                'type' => 'meet',
            ],
            [
                'title' => 'Link Meet Pertemuan ' . $request->session_num,
                'content_link' => $request->meet_url,
            ]
        );

        return response()->json(['message' => 'Meet link saved successfully', 'material' => $material]);
    }

    public function downloadMaterial(Request $request, $id)
    {
        $material = Material::findOrFail($id);
        
        if (str_starts_with($material->content_link, 'http')) {
            return redirect($material->content_link);
        }

        $path = str_replace('/storage/', '', $material->content_link);
        
        if (\Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
            return \Illuminate\Support\Facades\Storage::disk('local')->download($path);
        } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->download($path);
        }
        
        return response()->json(['message' => 'File not found'], 404);
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
            'mode' => 'nullable|string',
        ]);
        
        $attendance = \App\Models\Attendance::create([
            'course_id' => $courseId,
            'meeting_number' => $request->meeting_number,
            'date' => $request->date,
            'mode' => $request->mode ?? 'Online',
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
        \Log::info('updateGrade Request data for ' . $gradeId . ': ' . json_encode($request->all()));
        
        $request->validate([
            'attendance_score' => 'numeric|nullable|min:0|max:100',
            'assignment_score' => 'numeric|nullable|min:0|max:100',
            'uts_score' => 'numeric|nullable|min:0|max:100',
            'uas_score' => 'numeric|nullable|min:0|max:100',
            'score' => 'numeric|nullable|min:0|max:100',
            'grade' => 'string|nullable',
        ]);
        
        $grade = \App\Models\Grade::findOrFail($gradeId);
        $grade->update([
            'attendance_score' => $request->attendance_score,
            'assignment_score' => $request->assignment_score,
            'uts_score' => $request->uts_score,
            'uas_score' => $request->uas_score,
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
        $users = User::orderBy('role')->orderBy('name')->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'nim_nip' => $user->nim_nip,
                'role' => $user->role,
                'prodi' => $user->prodi,
                'jfa' => $user->jfa,
                'status' => $user->status,
                'phone' => $user->phone,
                'email' => $user->email,
            ];
        });

        return response()->json($users);
    }

    public function createUser(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'nim_nip' => 'required|unique:users',
            'role' => 'required|in:admin,superadmin,kaprodi,dosen,mahasiswa',
            'password' => 'required|min:6',
            'jfa' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'nim_nip' => $request->nim_nip,
            'role' => $request->role,
            'prodi' => $request->prodi,
            'jfa' => $request->jfa ?? 'Asisten Ahli',
            'status' => $request->status ?? 'Aktif',
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'User created', 'user' => $user]);
    }

    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'nim_nip' => 'required|unique:users,nim_nip,'.$id,
            'role' => 'required|in:admin,superadmin,kaprodi,dosen,mahasiswa',
            'jfa' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $user = User::findOrFail($id);
        $updateData = [
            'name' => $request->name,
            'nim_nip' => $request->nim_nip,
            'role' => $request->role,
            'prodi' => $request->prodi,
            'jfa' => $request->jfa ?? $user->jfa,
            'status' => $request->status ?? $user->status,
        ];
        
        if ($request->password) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return response()->json(['message' => 'User updated', 'user' => $user]);
    }

    public function deleteUser($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['message' => 'User deleted']);
    }

    public function getCourses()
    {
        $courses = Course::with('dosen')->orderBy('code')->get()->map(function ($course) {
            return [
                'id' => $course->id,
                'code' => $course->code,
                'name' => $course->name,
                'sks' => $course->sks,
                'semester' => $course->semester,
                'semester_num' => $course->semester_num,
                'type' => $course->type,
                'prodi' => $course->prodi,
                'dosen_id' => $course->dosen_id,
                'dosen' => $course->dosen ? [
                    'id' => $course->dosen->id,
                    'name' => $course->dosen->name,
                    'nim_nip' => $course->dosen->nim_nip,
                ] : null,
                'hari' => $course->hari,
                'jam_mulai' => $course->jam_mulai,
                'jam_selesai' => $course->jam_selesai,
                'ruang' => $course->ruang,
            ];
        });

        return response()->json($courses);
    }

    public function createCourse(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'code' => 'required|unique:courses',
            'sks' => 'required|numeric',
            'dosen_id' => 'required|exists:users,id',
            'semester_num' => 'nullable|integer',
            'type' => 'nullable|string',
            'prodi' => 'nullable|string',
            'semester' => 'nullable|string',
            'attendance_weight' => 'nullable|numeric|min:0|max:100',
            'assignment_weight' => 'nullable|numeric|min:0|max:100',
            'uts_weight' => 'nullable|numeric|min:0|max:100',
            'uas_weight' => 'nullable|numeric|min:0|max:100',
        ]);

        $course = Course::create([
            'name' => $request->name,
            'code' => $request->code,
            'sks' => $request->sks,
            'dosen_id' => $request->dosen_id,
            'semester_num' => $request->semester_num ?? 1,
            'type' => $request->type ?? 'Wajib',
            'prodi' => $request->prodi ?? 'Teknik Komputer',
            'semester' => $request->semester ?? 'Ganjil 2026/2027',
            'attendance_weight' => $request->attendance_weight ?? 10,
            'assignment_weight' => $request->assignment_weight ?? 20,
            'uts_weight' => $request->uts_weight ?? 30,
            'uas_weight' => $request->uas_weight ?? 40,
        ]);

        return response()->json(['message' => 'Course created', 'course' => $course]);
    }

    public function updateCourse(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'code' => 'required|unique:courses,code,'.$id,
            'sks' => 'required|numeric',
            'dosen_id' => 'required|exists:users,id',
            'semester_num' => 'nullable|integer',
            'type' => 'nullable|string',
            'prodi' => 'nullable|string',
            'semester' => 'nullable|string',
            'attendance_weight' => 'nullable|numeric|min:0|max:100',
            'assignment_weight' => 'nullable|numeric|min:0|max:100',
            'uts_weight' => 'nullable|numeric|min:0|max:100',
            'uas_weight' => 'nullable|numeric|min:0|max:100',
        ]);

        $course = Course::findOrFail($id);
        $course->update([
            'name' => $request->name,
            'code' => $request->code,
            'sks' => $request->sks,
            'dosen_id' => $request->dosen_id,
            'semester_num' => $request->semester_num ?? $course->semester_num,
            'type' => $request->type ?? $course->type,
            'prodi' => $request->prodi ?? $course->prodi,
            'semester' => $request->semester ?? $course->semester,
            'attendance_weight' => $request->attendance_weight ?? $course->attendance_weight,
            'assignment_weight' => $request->assignment_weight ?? $course->assignment_weight,
            'uts_weight' => $request->uts_weight ?? $course->uts_weight,
            'uas_weight' => $request->uas_weight ?? $course->uas_weight,
        ]);

        return response()->json(['message' => 'Course updated', 'course' => $course]);
    }

    public function updateCourseWeights(Request $request, $courseId)
    {
        $request->validate([
            'attendance_weight' => 'required|numeric|min:0|max:100',
            'assignment_weight' => 'required|numeric|min:0|max:100',
            'uts_weight' => 'required|numeric|min:0|max:100',
            'uas_weight' => 'required|numeric|min:0|max:100',
        ]);

        $course = Course::where('id', $courseId)
            ->where('dosen_id', $request->user()->id)
            ->firstOrFail();

        $course->update([
            'attendance_weight' => $request->attendance_weight,
            'assignment_weight' => $request->assignment_weight,
            'uts_weight' => $request->uts_weight,
            'uas_weight' => $request->uas_weight,
        ]);

        return response()->json(['message' => 'Grading weights updated', 'course' => $course]);
    }

    public function deleteCourse($id)
    {
        Course::findOrFail($id)->delete();
        return response()->json(['message' => 'Course deleted']);
    }

    public function payBilling(Request $request, $id)
    {
        $billing = \App\Models\Billing::where('id', $id)->where('user_id', $request->user()->id)->firstOrFail();
        $billing->update(['status' => 'Lunas']);
        return response()->json(['message' => 'Payment successful', 'billing' => $billing]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6'
        ]);
        
        $user = $request->user();
        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password)
        ]);

        return response()->json(['message' => 'Password updated successfully']);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'email' => 'nullable|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'bio' => 'nullable|string',
        ]);

        $user->update([
            'email' => $request->email ?? $user->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'bio' => $request->bio,
        ]);

        return response()->json(['message' => 'Profil berhasil diperbarui', 'user' => $user]);
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = auth()->user();
        
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/avatars'), $filename);
            
            $user->update([
                'avatar_url' => url('/uploads/avatars/' . $filename)
            ]);
            
            return response()->json(['message' => 'Avatar berhasil diunggah', 'avatar_url' => $user->avatar_url]);
        }

        return response()->json(['message' => 'Gagal mengunggah avatar'], 400);
    }
    public function getBillings()
    {
        return response()->json(\App\Models\Billing::with('user')->orderBy('created_at', 'desc')->get());
    }

    public function createBilling(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'description' => 'required',
            'amount' => 'required|numeric',
            'due_date' => 'required|date'
        ]);

        $billing = \App\Models\Billing::create([
            'user_id' => $request->user_id,
            'description' => $request->description,
            'amount' => $request->amount,
            'due_date' => $request->due_date,
            'status' => 'Belum Lunas'
        ]);

        return response()->json(['message' => 'Billing created successfully', 'billing' => $billing]);
    }

    public function updateBilling(Request $request, $id)
    {
        $request->validate([
            'description' => 'required',
            'amount' => 'required|numeric',
            'due_date' => 'required|date',
            'status' => 'required|in:Lunas,Belum Lunas'
        ]);

        $billing = \App\Models\Billing::findOrFail($id);
        $billing->update([
            'description' => $request->description,
            'amount' => $request->amount,
            'due_date' => $request->due_date,
            'status' => $request->status
        ]);

        return response()->json(['message' => 'Billing updated successfully', 'billing' => $billing]);
    }

    public function deleteBilling($id)
    {
        \App\Models\Billing::findOrFail($id)->delete();
        return response()->json(['message' => 'Billing deleted successfully']);
    }

    public function bulkGenerateBillings(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'due_date' => 'required|date'
        ]);

        $mahasiswaUsers = User::where('role', 'mahasiswa')->get();

        if ($mahasiswaUsers->isEmpty()) {
            return response()->json(['message' => 'Tidak ada mahasiswa aktif ditemukan.'], 404);
        }

        $created = 0;
        $skipped = 0;

        foreach ($mahasiswaUsers as $mhs) {
            // Skip if same billing already exists for this user
            $exists = \App\Models\Billing::where('user_id', $mhs->id)
                ->where('description', $request->description)
                ->where('due_date', $request->due_date)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            \App\Models\Billing::create([
                'user_id' => $mhs->id,
                'description' => $request->description,
                'amount' => $request->amount,
                'due_date' => $request->due_date,
                'status' => 'Belum Lunas'
            ]);
            $created++;
        }

        return response()->json([
            'message' => "Berhasil membuat {$created} tagihan untuk mahasiswa aktif." . ($skipped > 0 ? " {$skipped} tagihan duplikat dilewati." : ''),
            'created' => $created,
            'skipped' => $skipped,
            'total_mahasiswa' => $mahasiswaUsers->count()
        ]);
    }

    public function getAvailableKrs(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'mahasiswa') {
            $courses = Course::with('dosen')->get();
            return response()->json($courses);
        }

        // Determine current semester based on entry year (NIM)
        $entryYear = 2025;
        $nim = $user->nim_nip;
        if (strlen($nim) >= 5) {
            $yearPart = substr($nim, 3, 2);
            if (is_numeric($yearPart)) {
                $entryYear = 2000 + (int)$yearPart;
            }
        }

        $targetSemester = 5;
        if ($entryYear === 2025) {
            $targetSemester = 2;
        } elseif ($entryYear === 2024) {
            $targetSemester = 4;
        } elseif ($entryYear === 2023) {
            $targetSemester = 6;
        } elseif ($entryYear === 2022) {
            $targetSemester = 8;
        } elseif ($entryYear === 2026) {
            $targetSemester = 1;
        }

        $courses = Course::with('dosen')
            ->where('prodi', $user->prodi)
            ->where('semester_num', $targetSemester)
            ->get();

        return response()->json($courses);
    }

    public function getKrsSubmission(Request $request)
    {
        $semester = $request->query('semester');
        $query = \App\Models\KrsSubmission::where('mahasiswa_id', $request->user()->id);
        
        if ($semester) {
            $query->where('semester', $semester);
        }
        
        $submission = $query->latest()->first();
        return response()->json($submission);
    }

    public function submitKrs(Request $request)
    {
        $request->validate([
            'course_ids' => 'required|array',
            'semester' => 'required|string'
        ]);

        $submission = \App\Models\KrsSubmission::updateOrCreate(
            ['mahasiswa_id' => $request->user()->id, 'semester' => $request->semester],
            ['course_ids' => $request->course_ids, 'status' => 'pending']
        );

        return response()->json(['message' => 'KRS submitted successfully', 'submission' => $submission]);
    }

    public function getPendingKrs(Request $request)
    {
        $submissions = \App\Models\KrsSubmission::with('mahasiswa')->orderBy('created_at', 'desc')->get();
        return response()->json($submissions);
    }

    public function approveKrs(Request $request, $id)
    {
        $submission = \App\Models\KrsSubmission::findOrFail($id);
        $submission->update(['status' => 'approved']);

        // Create Grades based on approved KRS
        foreach ($submission->course_ids as $courseId) {
            Grade::updateOrCreate(
                ['mahasiswa_id' => $submission->mahasiswa_id, 'course_id' => $courseId],
                ['score' => null, 'grade' => null]
            );
        }

        return response()->json(['message' => 'KRS approved successfully']);
    }

    public function rejectKrs(Request $request, $id)
    {
        $submission = \App\Models\KrsSubmission::findOrFail($id);
        $submission->update(['status' => 'rejected']);

        return response()->json(['message' => 'KRS rejected successfully']);
    }
    // Kaprodi Mega Update Methods
    public function getKaprodiStats(Request $request)
    {
        $courses = Course::count();
        $students = User::where('role', 'mahasiswa')->count();
        $dosens = User::where('role', 'dosen')->count();
        
        $present = \App\Models\AttendanceRecord::where('status', 'present')->count();
        $absent = \App\Models\AttendanceRecord::where('status', 'absent')->count();

        if ($present === 0 && $absent === 0) {
            $present = 85;
            $absent = 15;
        }

        $distribution = [
            ['name' => 'Hadir', 'value' => $present],
            ['name' => 'Absen', 'value' => $absent]
        ];
        
        return response()->json([
            'total_classes' => $courses,
            'total_students' => $students,
            'total_dosens' => $dosens,
            'attendance_distribution' => $distribution,
        ]);
    }

    public function getKaprodiMonitoring(Request $request)
    {
        $courses = Course::with(['dosen', 'materials', 'attendances'])->get();
        return response()->json($courses);
    }

    public function getKaprodiCourses(Request $request)
    {
        $courses = Course::with('dosen')->get()->map(function($course) {
            $course->jamMulai = $course->jam_mulai;
            $course->jamSelesai = $course->jam_selesai;
            return $course;
        });
        $dosens = User::where('role', 'dosen')->get();
        return response()->json([
            'courses' => $courses,
            'dosens' => $dosens
        ]);
    }

    public function plotDosen(Request $request, $id)
    {
        $request->validate(['dosen_id' => 'required|exists:users,id']);
        $course = Course::findOrFail($id);
        $course->update(['dosen_id' => $request->dosen_id]);
        return response()->json(['message' => 'Dosen assigned successfully']);
    }

    public function plotSchedule(Request $request, $id)
    {
        $request->validate([
            'hari' => 'required|string',
            'jamMulai' => 'required|string',
            'jamSelesai' => 'required|string',
            'ruang' => 'required|string',
        ]);
        $course = Course::findOrFail($id);
        $course->update([
            'hari' => $request->hari,
            'jam_mulai' => $request->jamMulai,
            'jam_selesai' => $request->jamSelesai,
            'ruang' => $request->ruang,
        ]);
        return response()->json(['message' => 'Schedule updated successfully', 'course' => $course]);
    }

    public function getKaprodiStudentGrades(Request $request)
    {
        $grades = Grade::with(['mahasiswa', 'course'])->get();
        return response()->json($grades);
    }

    public function getKaprodiEdom(Request $request)
    {
        $edoms = \App\Models\Edom::with(['dosen', 'mahasiswa', 'course'])->get();
        $dosens = User::where('role', 'dosen')->get();
        
        // Seed edoms if empty for demo
        if ($edoms->isEmpty() && $dosens->count() > 0) {
            foreach ($dosens as $dosen) {
                \App\Models\Edom::create([
                    'dosen_id' => $dosen->id,
                    'mahasiswa_id' => User::where('role', 'mahasiswa')->first()->id ?? 1,
                    'course_id' => Course::first()->id ?? 1,
                    'score' => ($dosen->id % 3) + 3,
                    'comment' => 'Dosen mengajar dengan sangat baik dan jelas.'
                ]);
            }
            $edoms = \App\Models\Edom::with(['dosen', 'mahasiswa', 'course'])->get();
        }
        
        return response()->json(['edoms' => $edoms, 'dosens' => $dosens]);
    }

    // Dosen Ultimate Mega Update Methods
    public function getDosenDashboard(Request $request)
    {
        $dosenId = auth()->id();
        $courses = Course::with(['grades', 'attendances', 'assignments'])->where('dosen_id', $dosenId)->get();

        $todaySchedule = $courses->map(function($course) {
            $latestAttendance = $course->attendances->sortByDesc('meeting_number')->first();
            return [
                'course_id' => $course->id,
                'course' => $course->name,
                'time' => ($course->jam_mulai && $course->jam_selesai) ? $course->jam_mulai . ' - ' . $course->jam_selesai : '-',
                'room' => $course->ruang ?? '-',
                'meeting' => $latestAttendance?->meeting_number ?? 1,
                'day' => $course->hari ?? null,
            ];
        })->values();

        $pendingGrades = $courses->sum(function ($course) {
            return $course->grades->whereNull('score')->count();
        });

        $pendingBap = $courses->filter(function ($course) {
            return !\App\Models\Bap::where('course_id', $course->id)->exists();
        })->count();

        $todos = collect([
            $pendingGrades > 0 ? "Ada {$pendingGrades} nilai yang belum diisi." : null,
            $courses->sum(fn ($course) => $course->assignments->count()) > 0 ? 'Ada tugas aktif yang perlu dipantau.' : null,
            $pendingBap > 0 ? 'Beberapa mata kuliah belum memiliki BAP awal.' : null,
        ])->filter()->values();

        return response()->json([
            'total_courses' => $courses->count(),
            'schedule' => $todaySchedule,
            'todos' => $todos,
            'courses' => $courses,
            'semester' => 'Ganjil 2026/2027',
        ]);
    }

    public function storeBap(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'meeting_number' => 'required|integer',
            'topic' => 'required|string',
            'date' => 'required|date'
        ]);

        $bap = \App\Models\Bap::create([
            'course_id' => $request->course_id,
            'dosen_id' => auth()->id(),
            'meeting_number' => $request->meeting_number,
            'topic' => $request->topic,
            'date' => $request->date,
            'notes' => $request->notes
        ]);

        return response()->json(['message' => 'BAP berhasil disimpan', 'bap' => $bap]);
    }

    public function storeQuiz(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string',
            'duration_minutes' => 'required|integer',
            'questions' => 'required|array'
        ]);

        $quiz = \App\Models\Quiz::create([
            'course_id' => $request->course_id,
            'title' => $request->title,
            'duration_minutes' => $request->duration_minutes,
            'randomize_questions' => $request->randomize_questions ?? false
        ]);

        foreach ($request->questions as $q) {
            \App\Models\QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => $q['question'],
                'option_a' => $q['option_a'],
                'option_b' => $q['option_b'],
                'option_c' => $q['option_c'],
                'option_d' => $q['option_d'],
                'correct_answer' => $q['correct_answer']
            ]);
        }

        return response()->json(['message' => 'Quiz berhasil dibuat']);
    }

    public function getQuizzesByCourse(Request $request, $courseId)
    {
        $quizzes = \App\Models\Quiz::with('questions')->where('course_id', $courseId)->get();
        return response()->json($quizzes);
    }

    public function getCourseSessions(Request $request, $courseId)
    {
        $materials = \App\Models\Material::where('course_id', $courseId)->get();
        
        $sessions = [];
        for ($i = 1; $i <= 14; $i++) {
            $sessionMaterials = $materials->where('session_num', $i)->where('type', 'file');
            $meetLinkObj = $materials->where('session_num', $i)->where('type', 'meet')->first();

            $sessions[] = [
                'session' => $i,
                'title' => 'Materi Pertemuan ' . $i,
                'materials' => $sessionMaterials->values(),
                'material_count' => $sessionMaterials->count(),
                'meet_link' => $meetLinkObj ? $meetLinkObj->content_link : null
            ];
        }
        return response()->json($sessions);
    }


    // ==========================================
    // MAHASISWA ULTIMATE MEGA UPDATE
    // ==========================================

    public function getMahasiswaDashboard(Request $request)
    {
        $user = $request->user();
        $user->loadMissing('dosenWali');
        $today = now();
        $approvedKrs = \App\Models\KrsSubmission::where('mahasiswa_id', $user->id)
            ->where('status', 'approved')
            ->latest()
            ->first();

        $courseIds = collect($approvedKrs?->course_ids ?? [])
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->values();

        if ($courseIds->isNotEmpty()) {
            $courses = Course::with(['dosen', 'attendances', 'assignments', 'quizzes.questions'])->whereIn('id', $courseIds)->get();
        } else {
            $courses = Course::with(['dosen', 'attendances', 'assignments', 'quizzes.questions'])
                ->where('prodi', $user->prodi)
                ->get();
        }

        $scheduleToday = $courses->map(function ($course) use ($today) {
            $attendance = $course->attendances->sortByDesc('meeting_number')->first();
            return [
                'day' => $course->hari ?? $today->locale('id')->translatedFormat('l'),
                'time' => trim(($course->jam_mulai ?? '') . ($course->jam_selesai ? ' - ' . $course->jam_selesai : '')) ?: '-',
                'course' => $course->name,
                'room' => $course->ruang ?? '-',
                'dosen' => $course->dosen?->name ?? '-',
                'meeting' => $attendance?->meeting_number ?? 1,
            ];
        })->filter(fn ($item) => $item['course'] !== '-')->values();

        $upcomingDeadlines = [];
        foreach ($courses as $course) {
            foreach ($course->assignments->take(1) as $assignment) {
                $dueInDays = now()->diffInDays(\Carbon\Carbon::parse($assignment->deadline), false);
                $upcomingDeadlines[] = [
                    'title' => $assignment->title,
                    'course' => $course->name,
                    'due_in_days' => max(0, abs((int) $dueInDays)),
                ];
            }
        }

        $billing = \App\Models\Billing::where('user_id', $user->id)->latest()->first();
        if ($billing) {
            $upcomingDeadlines[] = [
                'title' => 'Tagihan ' . $billing->description,
                'course' => $user->prodi,
                'due_in_days' => max(0, now()->diffInDays($billing->due_date, false)),
            ];
        }

        $consultations = $this->formatConsultationMessages($user);

        return response()->json([
            'advisor' => $user->dosenWali ? [
                'id' => $user->dosenWali->id,
                'name' => $user->dosenWali->name,
                'nidn' => $user->dosenWali->nim_nip,
                'prodi' => $user->dosenWali->prodi,
            ] : null,
            'krs_status' => $approvedKrs?->status ?? 'belum_diajukan',
            'krs_deadline' => $today->copy()->addDays(7)->toDateString(),
            'schedule_today' => $scheduleToday,
            'upcoming_deadlines' => collect($upcomingDeadlines)->sortBy('due_in_days')->values(),
            'consultations' => $consultations->values(),
            'weekly_schedule' => $scheduleToday,
            'messages' => $consultations->values(),
        ]);
    }

    public function getMahasiswaConsultations(Request $request)
    {
        $user = $request->user();
        $user->loadMissing('dosenWali');

        return response()->json([
            'advisor' => $user->dosenWali ? [
                'id' => $user->dosenWali->id,
                'name' => $user->dosenWali->name,
                'nidn' => $user->dosenWali->nim_nip,
                'prodi' => $user->dosenWali->prodi,
            ] : null,
            'messages' => $this->formatConsultationMessages($user)->values(),
        ]);
    }

    public function storeMahasiswaConsultation(Request $request)
    {
        $user = $request->user();
        $user->loadMissing('dosenWali');

        if ($user->role !== 'mahasiswa' || !$user->dosenWali) {
            return response()->json(['message' => 'Dosen wali tidak ditemukan.'], 422);
        }

        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $message = ConsultationMessage::create([
            'mahasiswa_id' => $user->id,
            'dosen_id' => $user->dosenWali->id,
            'sender_role' => 'mahasiswa',
            'content' => trim($request->message),
            'is_read' => false,
        ]);

        return response()->json([
            'message' => 'Pesan konsultasi berhasil dikirim.',
            'data' => [
                'id' => $message->id,
                'sender' => 'mahasiswa',
                'content' => $message->content,
                'text' => $message->content,
                'created_at' => $message->created_at?->toDateTimeString(),
                'time' => $message->created_at?->format('j M Y, H:i'),
                'is_from_student' => true,
            ],
        ], 201);
    }

    public function getDosenConsultations(Request $request)
    {
        $dosen = $request->user();
        if ($dosen->role !== 'dosen' && $dosen->role !== 'kaprodi') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $students = User::where('dosen_wali_id', $dosen->id)
            ->where('role', 'mahasiswa')
            ->get()
            ->map(function ($student) use ($dosen) {
                $latestMsg = ConsultationMessage::where('mahasiswa_id', $student->id)
                    ->where('dosen_id', $dosen->id)
                    ->latest()
                    ->first();

                $unreadCount = ConsultationMessage::where('mahasiswa_id', $student->id)
                    ->where('dosen_id', $dosen->id)
                    ->where('sender_role', 'mahasiswa')
                    ->where('is_read', false)
                    ->count();

                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'nim' => $student->nim_nip,
                    'prodi' => $student->prodi,
                    'latest_message' => $latestMsg ? $latestMsg->content : null,
                    'latest_message_time' => $latestMsg ? $latestMsg->created_at?->format('j M Y, H:i') : null,
                    'unread_count' => $unreadCount,
                ];
            });

        return response()->json([
            'students' => $students
        ]);
    }

    public function getDosenStudentConsultation(Request $request, $mahasiswaId)
    {
        $dosen = $request->user();
        if ($dosen->role !== 'dosen' && $dosen->role !== 'kaprodi') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $student = User::where('id', $mahasiswaId)->where('dosen_wali_id', $dosen->id)->first();
        if (!$student) {
            return response()->json(['message' => 'Mahasiswa bimbingan tidak ditemukan.'], 404);
        }

        ConsultationMessage::where('mahasiswa_id', $student->id)
            ->where('dosen_id', $dosen->id)
            ->where('sender_role', 'mahasiswa')
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        $messages = ConsultationMessage::where('mahasiswa_id', $student->id)
            ->where('dosen_id', $dosen->id)
            ->orderBy('created_at')
            ->get()
            ->map(function ($message) {
                $sender = $message->sender_role === 'mahasiswa' ? 'mahasiswa' : 'dosen';
                return [
                    'id' => $message->id,
                    'sender' => $sender,
                    'content' => $message->content,
                    'text' => $message->content,
                    'created_at' => $message->created_at?->toDateTimeString(),
                    'time' => $message->created_at?->format('j M Y, H:i'),
                    'is_from_student' => $sender === 'mahasiswa',
                ];
            });

        return response()->json([
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'nim' => $student->nim_nip,
                'prodi' => $student->prodi,
            ],
            'messages' => $messages
        ]);
    }

    public function storeDosenConsultation(Request $request)
    {
        $dosen = $request->user();
        if ($dosen->role !== 'dosen' && $dosen->role !== 'kaprodi') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'mahasiswa_id' => 'required|exists:users,id',
            'message' => 'required|string|max:2000',
        ]);

        $student = User::where('id', $request->mahasiswa_id)->where('dosen_wali_id', $dosen->id)->first();
        if (!$student) {
            return response()->json(['message' => 'Mahasiswa bimbingan tidak ditemukan.'], 404);
        }

        $message = ConsultationMessage::create([
            'mahasiswa_id' => $student->id,
            'dosen_id' => $dosen->id,
            'sender_role' => 'dosen',
            'content' => trim($request->message),
            'is_read' => false,
        ]);

        return response()->json([
            'message' => 'Pesan konsultasi berhasil dikirim.',
            'data' => [
                'id' => $message->id,
                'sender' => 'dosen',
                'content' => $message->content,
                'text' => $message->content,
                'created_at' => $message->created_at?->toDateTimeString(),
                'time' => $message->created_at?->format('j M Y, H:i'),
                'is_from_student' => false,
            ],
        ], 201);
    }

    public function getMahasiswaMaterials(Request $request, $courseId)
    {
        $materials = \App\Models\Material::where('course_id', $courseId)->orderBy('session_num')->get();
        $sessions = [];
        for ($i = 1; $i <= 14; $i++) {
            $sessionMaterials = $materials->where('session_num', $i)->values();
            $sessions[] = [
                'session' => $i,
                'title' => 'Pertemuan ' . $i,
                'materials' => $sessionMaterials,
            ];
        }
        return response()->json($sessions);
    }

    public function getMahasiswaPresensi(Request $request)
    {
        $user = $request->user();
        $approvedKrs = \App\Models\KrsSubmission::where('mahasiswa_id', $user->id)
            ->where('status', 'approved')
            ->latest()
            ->first();

        $courseIds = collect($approvedKrs?->course_ids ?? [])
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->values();

        if ($courseIds->isNotEmpty()) {
            $courses = \App\Models\Course::with('attendances')->whereIn('id', $courseIds)->get();
        } else {
            $courses = \App\Models\Course::with('attendances')->where('prodi', $user->prodi)->get();
        }

        $presensiList = [];
        foreach ($courses as $c) {
            $latestAttendance = \App\Models\Attendance::where('course_id', $c->id)->latest()->first();
            $active_session = null;
            
            if ($latestAttendance) {
                $active_session = [
                    'id' => $latestAttendance->id,
                    'meeting' => $latestAttendance->meeting_number,
                    'status' => 'open',
                    'mode' => $latestAttendance->mode ?? 'Online'
                ];
            }

            $presensiList[] = [
                'course_name' => $c->name,
                'course_code' => $c->code,
                'total_meetings' => 14,
                'attended' => \App\Models\AttendanceRecord::whereHas('attendance', function($q) use ($c) {
                    $q->where('course_id', $c->id);
                })->where('mahasiswa_id', $request->user()->id)->where('status', 'present')->count(),
                'active_session' => $active_session
            ];
        }
        return response()->json($presensiList);
    }

    public function submitMahasiswaPresensi(Request $request, $attendanceId)
    {
        \App\Models\AttendanceRecord::updateOrCreate(
            ['attendance_id' => $attendanceId, 'mahasiswa_id' => $request->user()->id],
            ['status' => 'present']
        );
        return response()->json(['message' => 'Kehadiran berhasil dicatat.']);
    }

    public function getQuizForMahasiswa(Request $request, $quizId)
    {
        $quiz = \App\Models\Quiz::with('questions')->find($quizId);
        if (!$quiz) return response()->json(['message' => 'Quiz not found'], 404);
        
        // Hide correct answers
        $quizData = $quiz->toArray();
        foreach($quizData['questions'] as &$q) {
            unset($q['correct_answer']);
        }
        
        return response()->json($quizData);
    }

    public function submitQuizAnswers(Request $request, $quizId)
    {
        $request->validate([
            'answers' => 'required|array',
        ]);

        $quiz = \App\Models\Quiz::with('questions')->findOrFail($quizId);
        $answers = $request->answers;
        $correct = 0;
        $total = $quiz->questions->count();

        foreach ($quiz->questions as $question) {
            $given = $answers[$question->id] ?? null;
            $expected = strtoupper(trim((string) $question->correct_answer));

            if ($expected !== '' && strtoupper(trim((string) $given)) === $expected) {
                $correct++;
            }
        }

        $score = $total > 0 ? round(($correct / $total) * 100, 2) : 0;

        return response()->json([
            'message' => 'Jawaban kuis berhasil dikumpulkan.',
            'score' => $score,
            'correct' => $correct,
            'total' => $total,
        ]);
    }

    public function getMahasiswaGradebook(Request $request)
    {
        $user = $request->user();
        $gradesDb = \App\Models\Grade::with('course')->where('mahasiswa_id', $user->id)->get();
        
        $grades = [];
        foreach ($gradesDb as $g) {
            $c = $g->course;
            if (!$c) continue;

            $nilaiTugas = $g->assignment_score ?? 0;
            $nilaiKuis = $g->attendance_score ?? 0;
            $nilaiUts = $g->uts_score ?? 0;
            $nilaiUas = $g->uas_score ?? 0;
            $finalScore = $g->score;

            if ($finalScore === null) {
                $finalScore = round(($nilaiTugas * 0.2) + ($nilaiKuis * 0.2) + ($nilaiUts * 0.3) + ($nilaiUas * 0.3), 1);
            }

            $grades[] = [
                'course_name' => $c->name,
                'sks' => $c->sks,
                'tugas' => round($nilaiTugas, 1),
                'kuis' => round($nilaiKuis, 1),
                'uts' => round($nilaiUts, 1),
                'uas' => round($nilaiUas, 1),
                'attendance_weight' => (float) ($c->attendance_weight ?? 0),
                'assignment_weight' => (float) ($c->assignment_weight ?? 0),
                'uts_weight' => (float) ($c->uts_weight ?? 0),
                'uas_weight' => (float) ($c->uas_weight ?? 0),
                'akhir' => round((float) $finalScore, 1),
                'huruf' => $g->grade ?? $this->scoreToLetter((float) $finalScore),
                'semester_num' => (int) ($c->semester_num ?? 1),
                'semester_name' => $c->semester ?? '',
            ];
        }
        return response()->json($grades);
    }

    public function getDosenRoster(Request $request)
    {
        $dosenId = $request->user()->id;
        $courses = Course::with('grades.mahasiswa')->where('dosen_id', $dosenId)->get();
        
        $mappedCourses = $courses->map(function ($course) {
            $students = [];
            foreach ($course->grades as $grade) {
                if ($grade->mahasiswa) {
                    $students[] = [
                        'id' => $grade->mahasiswa->id,
                        'nim' => $grade->mahasiswa->nim_nip,
                        'name' => $grade->mahasiswa->name,
                        'prodi' => $grade->mahasiswa->prodi,
                        'phone' => $grade->mahasiswa->phone,
                    ];
                }
            }
            $course->students = $students;
            return $course;
        });

        return response()->json(['courses' => $mappedCourses]);
    }

    public function updateDosenJadwal(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'day' => 'required|string',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
            'room' => 'required|string',
        ]);
        
        $course = Course::where('id', $request->course_id)
            ->where('dosen_id', $request->user()->id)
            ->firstOrFail();
            
        $course->update([
            'hari' => $request->day,
            'jam_mulai' => $request->start_time,
            'jam_selesai' => $request->end_time,
            'ruang' => $request->room,
        ]);
        
        return response()->json(['message' => 'Jadwal updated', 'course' => $course]);
    }

    public function getDosenKrs(Request $request)
    {
        $dosenId = $request->user()->id;
        $krs = \App\Models\KrsSubmission::whereHas('mahasiswa', function($q) use ($dosenId) {
            $q->where('dosen_wali_id', $dosenId);
        })->with('mahasiswa')->get();

        $mappedKrs = $krs->map(function ($sub) {
            $courseIds = $sub->course_ids ?? [];
            $courses = \App\Models\Course::whereIn('id', $courseIds)->get();

            if ($sub->mahasiswa) {
                $sub->mahasiswa->nim = $sub->mahasiswa->nim_nip;
            }

            $sub->courses = $courses;
            return $sub;
        });

        return response()->json(['submissions' => $mappedKrs]);
    }

    public function approveDosenKrs(Request $request)
    {
        $krsId = $request->submission_id ?? $request->krs_id;

        $request->merge(['resolved_krs_id' => $krsId]);
        $request->validate([
            'resolved_krs_id' => 'required|exists:krs_submissions,id',
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string'
        ]);
        
        $krs = \App\Models\KrsSubmission::findOrFail($krsId);
        $krs->update([
            'status' => $request->status,
            'notes' => $request->notes
        ]);
        
        if ($request->status === 'approved') {
            foreach ($krs->course_ids as $courseId) {
                Grade::updateOrCreate(
                    ['mahasiswa_id' => $krs->mahasiswa_id, 'course_id' => $courseId],
                    ['score' => null, 'grade' => null]
                );
            }
        }
        
        return response()->json(['message' => 'KRS ' . $request->status, 'krs' => $krs]);
    }

    public function getDosenRekapPresensi(Request $request)
    {
        $dosenId = $request->user()->id;
        $courses = Course::where('dosen_id', $dosenId)->get();
        
        $mappedCourses = [];
        foreach($courses as $c) {
            $attendances = \App\Models\Attendance::where('course_id', $c->id)->pluck('id');
            $totalMeetings = $attendances->count();
            
            $grades = Grade::with('mahasiswa')->where('course_id', $c->id)->get();
            $students = [];
            
            foreach($grades as $g) {
                if (!$g->mahasiswa) continue;
                
                $presentCount = \App\Models\AttendanceRecord::whereIn('attendance_id', $attendances)
                    ->where('mahasiswa_id', $g->mahasiswa_id)
                    ->where('status', 'present')
                    ->count();
                
                $percentage = $totalMeetings > 0 ? round(($presentCount / $totalMeetings) * 100, 2) : 0;
                
                $students[] = [
                    'id' => $g->mahasiswa->id,
                    'nim' => $g->mahasiswa->nim_nip,
                    'name' => $g->mahasiswa->name,
                    'present_count' => $presentCount,
                    'attendance_percentage' => $percentage
                ];
            }
            
            $mappedCourses[] = [
                'id' => $c->id,
                'code' => $c->code,
                'name' => $c->name,
                'total_meetings' => $totalMeetings,
                'students' => $students
            ];
        }
        
        return response()->json(['courses' => $mappedCourses]);
    }

    public function importDosenGradebook(Request $request)
    {
        \Log::info('importDosenGradebook Request data: ' . json_encode($request->all()));
        
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'grades' => 'required|array',
            'grades.*.id' => 'nullable|exists:grades,id',
            'grades.*.mahasiswa_id' => 'nullable|exists:users,id',
            'grades.*.attendance_score' => 'nullable|numeric|min:0|max:100',
            'grades.*.assignment_score' => 'nullable|numeric|min:0|max:100',
            'grades.*.uts_score' => 'nullable|numeric|min:0|max:100',
            'grades.*.uas_score' => 'nullable|numeric|min:0|max:100',
            'grades.*.score' => 'nullable|numeric|min:0|max:100',
            'grades.*.grade' => 'nullable|string'
        ]);
        
        $course = Course::where('id', $request->course_id)
            ->where('dosen_id', $request->user()->id)
            ->firstOrFail();
            
        foreach ($request->grades as $g) {
            if (!empty($g['id'])) {
                $grade = Grade::where('id', $g['id'])
                    ->where('course_id', $course->id)
                    ->first();

                if ($grade) {
                    $grade->update([
                        'attendance_score' => $g['attendance_score'] ?? null,
                        'assignment_score' => $g['assignment_score'] ?? null,
                        'uts_score' => $g['uts_score'] ?? null,
                        'uas_score' => $g['uas_score'] ?? null,
                        'score' => $g['score'] ?? null,
                        'grade' => $g['grade'] ?? null,
                    ]);
                }
                continue;
            }

            if (empty($g['mahasiswa_id'])) {
                continue;
            }

            Grade::updateOrCreate(
                ['course_id' => $course->id, 'mahasiswa_id' => $g['mahasiswa_id']],
                [
                    'attendance_score' => $g['attendance_score'] ?? null,
                    'assignment_score' => $g['assignment_score'] ?? null,
                    'uts_score' => $g['uts_score'] ?? null,
                    'uas_score' => $g['uas_score'] ?? null,
                    'score' => $g['score'] ?? null,
                    'grade' => $g['grade'] ?? null
                ]
            );
        }
        
        $this->logActivity($request->user()->name, 'Import Nilai', 'Mengimpor nilai untuk kelas ' . $course->name);
        return response()->json(['message' => 'Grades imported successfully']);
    }

    // Helper for logging activity
    private function logActivity($userName, $action, $details = null)
    {
        \App\Models\ActivityLog::create([
            'user_name' => $userName,
            'action' => $action,
            'details' => $details,
            'ip_address' => request()->ip()
        ]);
    }

    // --- Academic Calendar CRUD ---
    public function getCalendar()
    {
        $events = \App\Models\AcademicCalendar::all()->map(function ($ev) {
            return [
                'id' => $ev->id,
                'name' => $ev->name,
                'startDate' => $ev->start_date,
                'endDate' => $ev->end_date,
                'type' => $ev->type,
            ];
        });
        return response()->json($events);
    }

    public function createCalendar(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'startDate' => 'required|date',
            'endDate' => 'required|date',
            'type' => 'required|string',
        ]);

        $ev = \App\Models\AcademicCalendar::create([
            'name' => $request->name,
            'start_date' => $request->startDate,
            'end_date' => $request->endDate,
            'type' => $request->type,
        ]);

        $this->logActivity($request->user()->name, 'Tambah Agenda Kalender', 'Menambahkan agenda: ' . $ev->name);

        return response()->json([
            'id' => $ev->id,
            'name' => $ev->name,
            'startDate' => $ev->start_date,
            'endDate' => $ev->end_date,
            'type' => $ev->type,
        ]);
    }

    public function updateCalendar(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'startDate' => 'required|date',
            'endDate' => 'required|date',
            'type' => 'required|string',
        ]);

        $ev = \App\Models\AcademicCalendar::findOrFail($id);
        $ev->update([
            'name' => $request->name,
            'start_date' => $request->startDate,
            'end_date' => $request->endDate,
            'type' => $request->type,
        ]);

        $this->logActivity($request->user()->name, 'Update Agenda Kalender', 'Memperbarui agenda: ' . $ev->name);

        return response()->json([
            'id' => $ev->id,
            'name' => $ev->name,
            'startDate' => $ev->start_date,
            'endDate' => $ev->end_date,
            'type' => $ev->type,
        ]);
    }

    public function deleteCalendar(Request $request, $id)
    {
        $ev = \App\Models\AcademicCalendar::findOrFail($id);
        $name = $ev->name;
        $ev->delete();

        $this->logActivity($request->user()->name, 'Hapus Agenda Kalender', 'Menghapus agenda: ' . $name);

        return response()->json(['message' => 'Event deleted']);
    }

    // --- Letter Request CRUD ---
    public function getMahasiswaLetters(Request $request)
    {
        $letters = \App\Models\LetterRequest::where('mahasiswa_id', $request->user()->id)
            ->orderBy('id', 'desc')
            ->get();
        return response()->json($letters);
    }

    public function submitMahasiswaLetter(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
        ]);

        $letter = \App\Models\LetterRequest::create([
            'mahasiswa_id' => $request->user()->id,
            'type' => $request->type,
            'date' => date('Y-m-d'),
            'status' => 'Pending',
            'note' => $request->note ?? 'Menunggu verifikasi admin',
        ]);

        $this->logActivity($request->user()->name, 'Ajukan Surat Keterangan', 'Mengajukan surat: ' . $letter->type);

        return response()->json($letter);
    }

    public function getAdminLetters()
    {
        $letters = \App\Models\LetterRequest::with('mahasiswa')->orderBy('id', 'desc')->get();
        return response()->json($letters);
    }

    public function updateLetterStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,Diproses,Selesai',
            'note' => 'nullable|string'
        ]);

        $letter = \App\Models\LetterRequest::findOrFail($id);
        $letter->update([
            'status' => $request->status,
            'note' => $request->note
        ]);

        $this->logActivity($request->user()->name, 'Update Status Surat', 'Mengubah status pengajuan surat ID ' . $id . ' menjadi ' . $letter->status);

        return response()->json($letter);
    }

    // --- Classroom CRUD ---
    public function getClassrooms()
    {
        return response()->json(\App\Models\Classroom::all());
    }

    public function createClassroom(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:classrooms,code',
            'name' => 'required|string',
            'capacity' => 'required|integer',
            'type' => 'required|string',
        ]);

        $room = \App\Models\Classroom::create($request->all());

        $this->logActivity($request->user()->name, 'Tambah Ruangan', 'Menambahkan ruang kelas: ' . $room->name);

        return response()->json($room);
    }

    public function updateClassroom(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|string|unique:classrooms,code,' . $id,
            'name' => 'required|string',
            'capacity' => 'required|integer',
            'type' => 'required|string',
        ]);

        $room = \App\Models\Classroom::findOrFail($id);
        $room->update($request->all());

        $this->logActivity($request->user()->name, 'Update Ruangan', 'Memperbarui ruang kelas: ' . $room->name);

        return response()->json($room);
    }

    public function deleteClassroom(Request $request, $id)
    {
        $room = \App\Models\Classroom::findOrFail($id);
        $name = $room->name;
        $room->delete();

        $this->logActivity($request->user()->name, 'Hapus Ruangan', 'Menghapus ruang kelas: ' . $name);

        return response()->json(['message' => 'Classroom deleted']);
    }

    // --- StudyProgram CRUD ---
    public function getStudyPrograms()
    {
        return response()->json(\App\Models\StudyProgram::all());
    }

    public function createStudyProgram(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:study_programs,code',
            'name' => 'required|string',
            'kaprodi' => 'nullable|string',
            'jenjang' => 'required|string',
        ]);

        $prodi = \App\Models\StudyProgram::create($request->all());

        $this->logActivity($request->user()->name, 'Tambah Prodi', 'Menambahkan program studi: ' . $prodi->name);

        return response()->json($prodi);
    }

    public function updateStudyProgram(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|string|unique:study_programs,code,' . $id,
            'name' => 'required|string',
            'kaprodi' => 'nullable|string',
            'jenjang' => 'required|string',
        ]);

        $prodi = \App\Models\StudyProgram::findOrFail($id);
        $prodi->update($request->all());

        $this->logActivity($request->user()->name, 'Update Prodi', 'Memperbarui program studi: ' . $prodi->name);

        return response()->json($prodi);
    }

    public function deleteStudyProgram(Request $request, $id)
    {
        $prodi = \App\Models\StudyProgram::findOrFail($id);
        $name = $prodi->name;
        $prodi->delete();

        $this->logActivity($request->user()->name, 'Hapus Prodi', 'Menghapus program studi: ' . $name);

        return response()->json(['message' => 'Study program deleted']);
    }

    // --- Logs & Backups ---
    public function getActivityLogs()
    {
        $logs = \App\Models\ActivityLog::orderBy('id', 'desc')->get();
        return response()->json($logs);
    }

    public function getBackups()
    {
        $backupPath = storage_path('app/public/backups');
        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        $files = glob($backupPath . '/*.sql');
        $backups = [];
        foreach ($files as $idx => $f) {
            $backups[] = [
                'id' => $idx + 1,
                'filename' => basename($f),
                'size' => round(filesize($f) / 1024, 2) . ' KB',
                'created_at' => date('Y-m-d H:i:s', filemtime($f)),
                'download_url' => asset('storage/backups/' . basename($f))
            ];
        }

        // Return latest backups first
        usort($backups, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return response()->json($backups);
    }

    public function triggerBackup(Request $request)
    {
        $backupPath = storage_path('app/public/backups');
        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        $dbFile = database_path('database.sqlite');
        $filename = 'backup_siakad_' . date('Ymd_His') . '.sql';
        $destFile = $backupPath . '/' . $filename;

        // Simply copy SQLite database or write a mock sql dump for SQLite
        if (file_exists($dbFile)) {
            copy($dbFile, $destFile);
        } else {
            // Write a dummy backup file if sqlite database file isn't found
            file_put_contents($destFile, "-- SIAKAD Backup --\n-- Date: " . date('Y-m-d H:i:s') . "\n");
        }

        $this->logActivity($request->user()->name, 'Backup Sistem', 'Memicu pembuatan backup database: ' . $filename);

        return response()->json([
            'message' => 'Backup created successfully',
            'backup' => [
                'filename' => $filename,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }

    public function deleteBackup(Request $request, $filename)
    {
        $backupPath = storage_path('app/public/backups/' . basename($filename));
        if (file_exists($backupPath)) {
            unlink($backupPath);
            $this->logActivity($request->user()->name, 'Hapus Backup', 'Menghapus file backup database: ' . $filename);
            return response()->json(['message' => 'Backup deleted']);
        }
        return response()->json(['message' => 'File not found'], 404);
    }


    public function exportKrsPdf(Request $request)
    {
        $user = $request->user();
        $user->loadMissing('dosenWali');

        $grades = Grade::with([
            'course.dosen',
        ])->where('mahasiswa_id', $user->id)->get();

        $totalSks = $grades->sum(function ($item) {
            return $item->course->sks ?? 0;
        });

        $semester = 'Semua';
        if ($grades->count() > 0) {
            $semester = $grades->first()->course->semester ?? 'Ganjil';
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.krs', [
            'user' => $user,
            'krs' => $grades,
            'totalSks' => $totalSks,
            'semester' => $semester
        ]);
        return $pdf->download('KRS_' . $user->nim_nip . '.pdf');
    }

    public function exportKhsPdf(Request $request)
    {
        $user = $request->user();
        $user->loadMissing('dosenWali');

        $grades = Grade::with([
            'course.dosen',
        ])->where('mahasiswa_id', $user->id)->get();

        $totalSks = $grades->sum(function ($item) {
            return $item->course->sks ?? 0;
        });

        $totalSksLulus = 0;
        $totalBobot = 0;
        foreach ($grades as $item) {
            if ($item->grade && !in_array($item->grade, ['E', 'D'])) {
                $totalSksLulus += $item->course->sks ?? 0;
            }
            $bobot = 0;
            if ($item->grade === 'A') $bobot = 4.0;
            elseif ($item->grade === 'A-') $bobot = 3.7;
            elseif ($item->grade === 'B+') $bobot = 3.3;
            elseif ($item->grade === 'B') $bobot = 3.0;
            elseif ($item->grade === 'B-') $bobot = 2.7;
            elseif ($item->grade === 'C+') $bobot = 2.3;
            elseif ($item->grade === 'C') $bobot = 2.0;
            elseif ($item->grade === 'D') $bobot = 1.0;
            elseif ($item->grade === 'E') $bobot = 0;
            
            $totalBobot += ($bobot * ($item->course->sks ?? 0));
        }
        $ipSemester = $totalSks > 0 ? ($totalBobot / $totalSks) : 0.0;

        $semester = 'Semua';
        if ($grades->count() > 0) {
            $semester = $grades->first()->course->semester ?? 'Ganjil';
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.khs', [
            'user' => $user,
            'krs' => $grades,
            'totalSks' => $totalSks,
            'totalSksLulus' => $totalSksLulus,
            'ipSemester' => $ipSemester,
            'semester' => $semester
        ]);
        return $pdf->download('KHS_' . $user->nim_nip . '.pdf');
    }

    public function gradeSubmission(Request $request, $submissionId)
    {
        $request->validate([
            'grade' => 'required|numeric|min:0|max:100',
        ]);

        $submission = Submission::findOrFail($submissionId);
        $submission->update(['grade' => $request->grade]);

        $assignment = $submission->assignment;
        if ($assignment) {
            $course = $assignment->course;
            $gradeRecord = Grade::where('course_id', $assignment->course_id)
                                ->where('mahasiswa_id', $submission->mahasiswa_id)
                                ->first();
            if ($gradeRecord) {
                $gradeRecord->update(['assignment_score' => $request->grade]);

                $attW = (float) ($course->attendance_weight ?? 10);
                $assW = (float) ($course->assignment_weight ?? 20);
                $utsW = (float) ($course->uts_weight ?? 30);
                $uasW = (float) ($course->uas_weight ?? 40);
                $totalW = $attW + $assW + $utsW + $uasW;

                $attS = (float) ($gradeRecord->attendance_score ?? 0);
                $assS = (float) $request->grade;
                $utsS = (float) ($gradeRecord->uts_score ?? 0);
                $uasS = (float) ($gradeRecord->uas_score ?? 0);

                if ($totalW > 0) {
                    $finalScore = (($attS * $attW) + ($assS * $assW) + ($utsS * $utsW) + ($uasS * $uasW)) / $totalW;
                } else {
                    $finalScore = 0;
                }

                $finalScore = round($finalScore, 1);
                $gradeLetter = $this->scoreToLetter($finalScore);

                $gradeRecord->update([
                    'score' => $finalScore,
                    'grade' => $gradeLetter
                ]);
            }
        }

        return response()->json(['message' => 'Submission graded successfully', 'submission' => $submission]);
    }

    public function downloadSubmission(Request $request, $submissionId)
    {
        $submission = Submission::findOrFail($submissionId);
        $path = str_replace('/storage/', '', $submission->file_path);
        
        if (!\Storage::disk('public')->exists($path)) {
            abort(404, 'File not found');
        }

        return \Storage::disk('public')->download($path);
    }

    public function getSettings()
    {
        $settings = \App\Models\Content::where('key', 'like', 'siakad_%')->pluck('value', 'key');
        return response()->json($settings);
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
        ]);

        foreach ($request->settings as $key => $value) {
            if (str_starts_with($key, 'siakad_')) {
                \App\Models\Content::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }

        $this->logActivity($request->user()->name, 'Update Pengaturan', 'Mengubah konfigurasi sistem akademik');

        return response()->json(['message' => 'Settings updated successfully']);
    }

    private function scoreToLetter(float $score): string
    {
        return match (true) {
            $score >= 85 => 'A',
            $score >= 80 => 'A-',
            $score >= 75 => 'B+',
            $score >= 70 => 'B',
            $score >= 65 => 'B-',
            $score >= 60 => 'C+',
            $score >= 55 => 'C',
            $score >= 40 => 'D',
            default => 'E',
        };
    }
}
