<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

$defaultNews = [
    [
        'id' => 1,
        'title' => 'Penerimaan Mahasiswa Baru Semester Gasal 2025/2026 Resmi Dibuka',
        'date' => '8 Juni 2026',
        'image_url' => 'https://umiba.ac.id/wp-content/uploads/2024/05/bannerUMIBA26_1.png',
        'source' => 'kompaskampus.id'
    ],
    [
        'id' => 2,
        'title' => 'Seminar Nasional Teknologi Informasi & Aktuaria 2025',
        'date' => '5 Juni 2026',
        'image_url' => 'https://umiba.ac.id/wp-content/uploads/2024/05/bannerUMIBA26_2.png',
        'source' => 'wartaekonomi.co.id'
    ],
    [
        'id' => 3,
        'title' => 'Mahasiswa UMIBA Raih Juara 1 Kompetisi Nasional 2025',
        'date' => '1 Juni 2026',
        'image_url' => 'https://umiba.ac.id/wp-content/uploads/2024/05/bannerUMIBA26_3.png',
        'source' => 'teropongsenayan.com'
    ],
    [
        'id' => 4,
        'title' => 'Universitas Mitra Bangsa Selenggarakan Gebyar Kemerdekaan HUT-RI Ke-80',
        'date' => '17 Agustus 2025',
        'image_url' => 'https://umiba.ac.id/wp-content/uploads/2025/08/umiba-upacara.jpg',
        'source' => 'newsdetik.co'
    ]
];

Route::get('/home-data', function () use ($defaultNews) {
    $news = \App\Models\News::orderBy('id', 'asc')->limit(3)->get();
    return response()->json([
        'news' => $news->isEmpty() ? array_slice($defaultNews, 0, 3) : $news,
        'testimonials' => \App\Models\Testimonial::all(),
        'contents' => \App\Models\Content::pluck('value', 'key')
    ]);
});

Route::get('/news', function () use ($defaultNews) {
    $news = \App\Models\News::orderBy('id', 'asc')->get();
    return response()->json([
        'news' => $news->isEmpty() ? $defaultNews : $news
    ]);
});

Route::get('/contents', [\App\Http\Controllers\AdminContentController::class, 'index']);

// Admin Auth Routes
Route::post('/admin/login', [\App\Http\Controllers\AuthController::class, 'login']);

// Protected Admin Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/admin/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
    
    // News
    Route::get('/admin/news', [\App\Http\Controllers\AdminNewsController::class, 'index']);
    Route::post('/admin/news', [\App\Http\Controllers\AdminNewsController::class, 'store']);
    Route::put('/admin/news/{id}', [\App\Http\Controllers\AdminNewsController::class, 'update']);
    Route::delete('/admin/news/{id}', [\App\Http\Controllers\AdminNewsController::class, 'destroy']);
    
    // Testimonials
    Route::get('/admin/testimonials', [\App\Http\Controllers\AdminTestimonialController::class, 'index']);
    Route::post('/admin/testimonials', [\App\Http\Controllers\AdminTestimonialController::class, 'store']);
    Route::delete('/admin/testimonials/{id}', [\App\Http\Controllers\AdminTestimonialController::class, 'destroy']);
    
    // Contents & Images
    Route::put('/admin/contents', [\App\Http\Controllers\AdminContentController::class, 'update']);
    Route::post('/admin/upload-image', [\App\Http\Controllers\AdminContentController::class, 'uploadImage']);
});

// Siakad Routes
Route::post('/siakad/login', [\App\Http\Controllers\SiakadController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/siakad/dashboard', [\App\Http\Controllers\SiakadController::class, 'dashboard']);
    Route::post('/siakad/course/{courseId}/materi', [\App\Http\Controllers\SiakadController::class, 'uploadMateri']);
    Route::post('/siakad/assignment/{assignmentId}/submit', [\App\Http\Controllers\SiakadController::class, 'uploadSubmission']);
    
    // Dosen operations
    Route::post('/siakad/course/{courseId}/assignment', [\App\Http\Controllers\SiakadController::class, 'createAssignment']);
    Route::post('/siakad/course/{courseId}/attendance', [\App\Http\Controllers\SiakadController::class, 'createAttendance']);
    Route::post('/siakad/attendance/{attendanceId}/record', [\App\Http\Controllers\SiakadController::class, 'updateAttendanceRecord']);
    Route::post('/siakad/grade/{gradeId}', [\App\Http\Controllers\SiakadController::class, 'updateGrade']);
    
    Route::post('/siakad/billing/{id}/pay', [\App\Http\Controllers\SiakadController::class, 'payBilling']);
    Route::post('/siakad/profile/password', [\App\Http\Controllers\SiakadController::class, 'updatePassword']);
    Route::post('/siakad/profile/update', [\App\Http\Controllers\SiakadController::class, 'updateProfile']);
    Route::post('/siakad/profile/upload-avatar', [\App\Http\Controllers\SiakadController::class, 'uploadAvatar']);
    Route::post('/siakad/profile/preferences', [\App\Http\Controllers\SiakadController::class, 'updatePreferences']);
    
    // Forum Diskusi
    Route::post('/siakad/forum/{courseId}', [\App\Http\Controllers\SiakadController::class, 'createForumThread']);
    Route::post('/siakad/forum/{forumId}/reply', [\App\Http\Controllers\SiakadController::class, 'replyForum']);
    
    // KRS Online
    Route::get('/siakad/krs/available', [\App\Http\Controllers\SiakadController::class, 'getAvailableKrs']);
    Route::get('/siakad/krs/submission', [\App\Http\Controllers\SiakadController::class, 'getKrsSubmission']);
    Route::post('/siakad/krs/submit', [\App\Http\Controllers\SiakadController::class, 'submitKrs']);
    Route::get('/siakad/krs/pending', [\App\Http\Controllers\SiakadController::class, 'getPendingKrs']);
    Route::post('/siakad/krs/approve/{id}', [\App\Http\Controllers\SiakadController::class, 'approveKrs']);
    Route::post('/siakad/krs/reject/{id}', [\App\Http\Controllers\SiakadController::class, 'rejectKrs']);

    // Kaprodi Mega Update
    Route::prefix('siakad/kaprodi')->group(function () {
        Route::get('/stats', [\App\Http\Controllers\SiakadController::class, 'getKaprodiStats']);
        Route::get('/monitoring', [\App\Http\Controllers\SiakadController::class, 'getKaprodiMonitoring']);
        Route::get('/courses', [\App\Http\Controllers\SiakadController::class, 'getKaprodiCourses']);
        Route::post('/courses/{id}/plot', [\App\Http\Controllers\SiakadController::class, 'plotDosen']);
        Route::post('/courses/{id}/schedule', [\App\Http\Controllers\SiakadController::class, 'plotSchedule']);
        Route::get('/students/grades', [\App\Http\Controllers\SiakadController::class, 'getKaprodiStudentGrades']);
        Route::get('/edom', [\App\Http\Controllers\SiakadController::class, 'getKaprodiEdom']);
    });

    // Admin Siakad operations
    Route::get('/siakad/admin/users', [\App\Http\Controllers\SiakadController::class, 'getUsers']);
    Route::post('/siakad/admin/users', [\App\Http\Controllers\SiakadController::class, 'createUser']);
    Route::put('/siakad/admin/users/{id}', [\App\Http\Controllers\SiakadController::class, 'updateUser']);
    Route::delete('/siakad/admin/users/{id}', [\App\Http\Controllers\SiakadController::class, 'deleteUser']);
    
    Route::get('/siakad/admin/courses', [\App\Http\Controllers\SiakadController::class, 'getCourses']);
    Route::post('/siakad/admin/courses', [\App\Http\Controllers\SiakadController::class, 'createCourse']);
    Route::put('/siakad/admin/courses/{id}', [\App\Http\Controllers\SiakadController::class, 'updateCourse']);
    Route::delete('/siakad/admin/courses/{id}', [\App\Http\Controllers\SiakadController::class, 'deleteCourse']);

    // Admin Billing operations
    Route::get('/siakad/admin/billings', [\App\Http\Controllers\SiakadController::class, 'getBillings']);
    Route::post('/siakad/admin/billings', [\App\Http\Controllers\SiakadController::class, 'createBilling']);
    Route::put('/siakad/admin/billings/{id}', [\App\Http\Controllers\SiakadController::class, 'updateBilling']);
    Route::delete('/siakad/admin/billings/{id}', [\App\Http\Controllers\SiakadController::class, 'deleteBilling']);
    Route::post('/siakad/admin/billings/bulk-generate', [\App\Http\Controllers\SiakadController::class, 'bulkGenerateBillings']);

    // Dosen Ultimate Mega Update
    Route::prefix('siakad/dosen')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\SiakadController::class, 'getDosenDashboard']);
        Route::post('/bap', [\App\Http\Controllers\SiakadController::class, 'storeBap']);
        Route::post('/quiz', [\App\Http\Controllers\SiakadController::class, 'storeQuiz']);
        Route::get('/courses/{courseId}/quizzes', [\App\Http\Controllers\SiakadController::class, 'getQuizzesByCourse']);
        Route::get('/courses/{courseId}/sessions', [\App\Http\Controllers\SiakadController::class, 'getCourseSessions']);
        Route::post('/course/{courseId}/materials', [\App\Http\Controllers\SiakadController::class, 'uploadMateri']);
        Route::post('/course/{courseId}/meet-link', [\App\Http\Controllers\SiakadController::class, 'saveMeetLink']);
        Route::post('/course/{courseId}/assignments', [\App\Http\Controllers\SiakadController::class, 'createAssignment']);
        
        Route::get('/roster', [\App\Http\Controllers\SiakadController::class, 'getDosenRoster']);
        Route::post('/jadwal/update', [\App\Http\Controllers\SiakadController::class, 'updateDosenJadwal']);
        Route::get('/krs', [\App\Http\Controllers\SiakadController::class, 'getDosenKrs']);
        Route::post('/krs/approve', [\App\Http\Controllers\SiakadController::class, 'approveDosenKrs']);
        Route::get('/rekap-presensi', [\App\Http\Controllers\SiakadController::class, 'getDosenRekapPresensi']);
        Route::post('/gradebook/import', [\App\Http\Controllers\SiakadController::class, 'importDosenGradebook']);
        Route::post('/gradebook/publish', [\App\Http\Controllers\SiakadController::class, 'publishDosenGradebook']);
        Route::put('/courses/{courseId}/grading-weights', [\App\Http\Controllers\SiakadController::class, 'updateCourseWeights']);
        
        // Bimbingan Akademik (Consultations) untuk Dosen
        Route::get('/consultations', [\App\Http\Controllers\SiakadController::class, 'getDosenConsultations']);
        Route::get('/consultations/{mahasiswaId}', [\App\Http\Controllers\SiakadController::class, 'getDosenStudentConsultation']);
        Route::post('/consultations', [\App\Http\Controllers\SiakadController::class, 'storeDosenConsultation']);
    });
    
    Route::get('/siakad/materials/download/{id}', [\App\Http\Controllers\SiakadController::class, 'downloadMaterial']);
    
    // Mahasiswa Ultimate Mega Update
    Route::prefix('siakad/mahasiswa')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\SiakadController::class, 'getMahasiswaDashboard']);
        Route::get('/consultations', [\App\Http\Controllers\SiakadController::class, 'getMahasiswaConsultations']);
        Route::post('/consultations', [\App\Http\Controllers\SiakadController::class, 'storeMahasiswaConsultation']);
        Route::get('/courses/{courseId}/materials', [\App\Http\Controllers\SiakadController::class, 'getMahasiswaMaterials']);
        Route::get('/presensi', [\App\Http\Controllers\SiakadController::class, 'getMahasiswaPresensi']);
        Route::post('/presensi/{attendanceId}/submit', [\App\Http\Controllers\SiakadController::class, 'submitMahasiswaPresensi']);
        Route::get('/quizzes/{quizId}', [\App\Http\Controllers\SiakadController::class, 'getQuizForMahasiswa']);
        Route::post('/quizzes/{quizId}/submit', [\App\Http\Controllers\SiakadController::class, 'submitQuizAnswers']);
        Route::get('/gradebook', [\App\Http\Controllers\SiakadController::class, 'getMahasiswaGradebook']);
    });

    // Academic Calendar
    Route::get('/siakad/calendar', [\App\Http\Controllers\SiakadController::class, 'getCalendar']);
    Route::post('/siakad/calendar', [\App\Http\Controllers\SiakadController::class, 'createCalendar']);
    Route::put('/siakad/calendar/{id}', [\App\Http\Controllers\SiakadController::class, 'updateCalendar']);
    Route::delete('/siakad/calendar/{id}', [\App\Http\Controllers\SiakadController::class, 'deleteCalendar']);

    // Schedule Swap Overrides
    Route::get('siakad/schedules/calendar', [\App\Http\Controllers\Siakad\ScheduleController::class, 'getCalendarView']);
    Route::post('siakad/schedules/override', [\App\Http\Controllers\Siakad\ScheduleController::class, 'createOverride']);


    // Settings
    Route::get('/siakad/settings', [\App\Http\Controllers\SiakadController::class, 'getSettings']);
    Route::post('/siakad/settings', [\App\Http\Controllers\SiakadController::class, 'updateSettings']);

    // Letter Requests
    Route::get('/siakad/mahasiswa/letters', [\App\Http\Controllers\SiakadController::class, 'getMahasiswaLetters']);
    Route::post('/siakad/mahasiswa/letters', [\App\Http\Controllers\SiakadController::class, 'submitMahasiswaLetter']);
    Route::get('/siakad/admin/letters', [\App\Http\Controllers\SiakadController::class, 'getAdminLetters']);
    Route::put('/siakad/admin/letters/{id}', [\App\Http\Controllers\SiakadController::class, 'updateLetterStatus']);

    // Classrooms
    Route::get('/siakad/admin/classrooms', [\App\Http\Controllers\SiakadController::class, 'getClassrooms']);
    Route::post('/siakad/admin/classrooms', [\App\Http\Controllers\SiakadController::class, 'createClassroom']);
    Route::put('/siakad/admin/classrooms/{id}', [\App\Http\Controllers\SiakadController::class, 'updateClassroom']);
    Route::delete('/siakad/admin/classrooms/{id}', [\App\Http\Controllers\SiakadController::class, 'deleteClassroom']);

    // Study Programs
    Route::get('/siakad/admin/prodis', [\App\Http\Controllers\SiakadController::class, 'getStudyPrograms']);
    Route::post('/siakad/admin/prodis', [\App\Http\Controllers\SiakadController::class, 'createStudyProgram']);
    Route::put('/siakad/admin/prodis/{id}', [\App\Http\Controllers\SiakadController::class, 'updateStudyProgram']);
    Route::delete('/siakad/admin/prodis/{id}', [\App\Http\Controllers\SiakadController::class, 'deleteStudyProgram']);

    // Logs & Backups
    Route::get('/siakad/admin/logs', [\App\Http\Controllers\SiakadController::class, 'getActivityLogs']);
    Route::get('/siakad/admin/backups', [\App\Http\Controllers\SiakadController::class, 'getBackups']);
    Route::post('/siakad/admin/backups', [\App\Http\Controllers\SiakadController::class, 'triggerBackup']);
    Route::delete('/siakad/admin/backups/{filename}', [\App\Http\Controllers\SiakadController::class, 'deleteBackup']);

    // PDDIKTI Neo Feeder Operations
    Route::prefix('siakad/admin/feeder')->group(function () {
        Route::get('/test-connection', [\App\Http\Controllers\Siakad\NeoFeederController::class, 'testConnection']);
        Route::get('/stats', [\App\Http\Controllers\Siakad\NeoFeederController::class, 'getStats']);
        Route::post('/sync', [\App\Http\Controllers\Siakad\NeoFeederController::class, 'triggerSync']);
    });

    // Chat Real-Time
    Route::prefix('siakad/chat')->group(function () {
        Route::get('/rooms', [\App\Http\Controllers\Siakad\ChatController::class, 'index']);
        Route::post('/rooms', [\App\Http\Controllers\Siakad\ChatController::class, 'createRoom']);
        Route::get('/rooms/{id}', [\App\Http\Controllers\Siakad\ChatController::class, 'show']);
        Route::post('/rooms/{id}/messages', [\App\Http\Controllers\Siakad\ChatController::class, 'store']);
    });

    // MBKM
    Route::prefix('siakad/mbkm')->group(function () {
        Route::get('/programs', [\App\Http\Controllers\Siakad\MbkmController::class, 'index']);
        Route::post('/programs', [\App\Http\Controllers\Siakad\MbkmController::class, 'store']);
        Route::get('/programs/{id}', [\App\Http\Controllers\Siakad\MbkmController::class, 'show']);
        Route::post('/programs/{id}/submit', [\App\Http\Controllers\Siakad\MbkmController::class, 'submit']);
        Route::post('/submissions/{id}/approve', [\App\Http\Controllers\Siakad\MbkmController::class, 'approve']);
        Route::delete('/programs/{id}', [\App\Http\Controllers\Siakad\MbkmController::class, 'destroy']);
    });

    // Proctoring
    Route::prefix('siakad/proctoring')->group(function () {
        Route::get('/sessions', [\App\Http\Controllers\Siakad\ProctoringController::class, 'index']);
        Route::post('/sessions/join', [\App\Http\Controllers\Siakad\ProctoringController::class, 'join']);
        Route::post('/generate-token', [\App\Http\Controllers\Siakad\ProctoringController::class, 'generateToken']);
        Route::post('/sessions/{id}/start', [\App\Http\Controllers\Siakad\ProctoringController::class, 'start']);
        Route::post('/sessions/{id}/stop', [\App\Http\Controllers\Siakad\ProctoringController::class, 'stop']);
        Route::post('/log', [\App\Http\Controllers\Siakad\ProctoringController::class, 'logEvent']);
        Route::get('/sessions/{id}/logs', [\App\Http\Controllers\Siakad\ProctoringController::class, 'getSessionLogs']);
    });

    // Tracer Study & Alumni
    Route::prefix('siakad/tracer')->group(function () {
        Route::get('/alumni', [\App\Http\Controllers\Siakad\TracerController::class, 'index']);
        Route::post('/alumni', [\App\Http\Controllers\Siakad\TracerController::class, 'storeAlumni']);
        Route::post('/survey', [\App\Http\Controllers\Siakad\TracerController::class, 'storeSurvey']);
        Route::get('/stats', [\App\Http\Controllers\Siakad\TracerController::class, 'stats']);
        Route::get('/export', [\App\Http\Controllers\Siakad\TracerController::class, 'exportCsv']);
    });

    // PMB (Penerimaan Mahasiswa Baru) - Admin routes (require auth)
    Route::prefix('siakad/pmb')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Siakad\PmbController::class, 'dashboard']);
        Route::post('/periods', [\App\Http\Controllers\Siakad\PmbController::class, 'createPeriod']);
        Route::put('/periods/{id}', [\App\Http\Controllers\Siakad\PmbController::class, 'updatePeriod']);
        Route::get('/applicants/{periodId}', [\App\Http\Controllers\Siakad\PmbController::class, 'getApplicants']);
        Route::get('/applicant/{applicantId}', [\App\Http\Controllers\Siakad\PmbController::class, 'getApplicantDetail']);
        Route::patch('/status/{applicantId}', [\App\Http\Controllers\Siakad\PmbController::class, 'updateStatus']);
    });

    // PDF Exports & Grading
    Route::get('/siakad/export/krs', [\App\Http\Controllers\SiakadController::class, 'exportKrsPdf']);
    Route::get('/siakad/export/khs', [\App\Http\Controllers\SiakadController::class, 'exportKhsPdf']);
    Route::post('/siakad/submission/{submissionId}/grade', [\App\Http\Controllers\SiakadController::class, 'gradeSubmission']);
    Route::get('/siakad/submission/{submissionId}/download', [\App\Http\Controllers\SiakadController::class, 'downloadSubmission']);

    // SKPI & Prestasi
    Route::prefix('siakad/skpi')->group(function () {
        Route::get('/list', [\App\Http\Controllers\Siakad\SkpiController::class, 'index']);
        Route::post('/submit', [\App\Http\Controllers\Siakad\SkpiController::class, 'submit']);
        Route::post('/verify/{id}', [\App\Http\Controllers\Siakad\SkpiController::class, 'approve']);
        Route::post('/skpi-submit', [\App\Http\Controllers\Siakad\SkpiController::class, 'submitSkpi']);
        Route::post('/skpi-verify/{id}', [\App\Http\Controllers\Siakad\SkpiController::class, 'approveSkpi']);
    });

    // Yudisium & Wisuda
    Route::prefix('siakad/graduation')->group(function () {
        Route::get('/yudisium', [\App\Http\Controllers\Siakad\GraduationController::class, 'getYudisiumList']);
        Route::post('/yudisium', [\App\Http\Controllers\Siakad\GraduationController::class, 'applyYudisium']);
        Route::post('/yudisium/{id}/verify', [\App\Http\Controllers\Siakad\GraduationController::class, 'verifyYudisium']);
        Route::get('/wisuda', [\App\Http\Controllers\Siakad\GraduationController::class, 'getWisudaList']);
        Route::post('/wisuda', [\App\Http\Controllers\Siakad\GraduationController::class, 'applyWisuda']);
        Route::post('/wisuda/{id}/confirm', [\App\Http\Controllers\Siakad\GraduationController::class, 'confirmWisuda']);
    });

    // Litabmas (Penelitian & Pengabdian)
    Route::prefix('siakad/litabmas')->group(function () {
        Route::get('/proposals', [\App\Http\Controllers\Siakad\LitabmasController::class, 'index']);
        Route::post('/proposals', [\App\Http\Controllers\Siakad\LitabmasController::class, 'store']);
        Route::post('/proposals/{id}/review', [\App\Http\Controllers\Siakad\LitabmasController::class, 'review']);
    });

    // EDOM (Evaluasi Dosen oleh Mahasiswa)
    Route::prefix('siakad/edom')->group(function () {
        Route::get('/my-courses', [\App\Http\Controllers\Siakad\EdomController::class, 'getMyCourses']);
        Route::get('/questions', [\App\Http\Controllers\Siakad\EdomController::class, 'getQuestions']);
        Route::post('/submit', [\App\Http\Controllers\Siakad\EdomController::class, 'submitAnswers']);
        Route::get('/stats/{dosenId}', [\App\Http\Controllers\Siakad\EdomController::class, 'getDosenStats']);
    });

    // Penjaminan Mutu (SPMI/SPME) & IKU
    Route::prefix('siakad/qa')->group(function () {
        Route::get('/spmi', [\App\Http\Controllers\Siakad\QualityAssuranceController::class, 'getSpmiDocs']);
        Route::post('/spmi', [\App\Http\Controllers\Siakad\QualityAssuranceController::class, 'uploadSpmiDoc']);
        Route::get('/spme', [\App\Http\Controllers\Siakad\QualityAssuranceController::class, 'getSpmeDocs']);
        Route::post('/spme', [\App\Http\Controllers\Siakad\QualityAssuranceController::class, 'uploadSpmeDoc']);
        Route::get('/survey', [\App\Http\Controllers\Siakad\QualityAssuranceController::class, 'getSurveyStats']);
        Route::get('/iku', [\App\Http\Controllers\Siakad\QualityAssuranceController::class, 'getIkuStats']);
    });

    // Kepegawaian (HRD)
    Route::prefix('siakad/hrd')->group(function () {
        Route::get('/employees', [\App\Http\Controllers\Siakad\HrdController::class, 'index']);
        Route::post('/employees', [\App\Http\Controllers\Siakad\HrdController::class, 'store']);
        Route::put('/employees/{id}', [\App\Http\Controllers\Siakad\HrdController::class, 'update']);
        Route::delete('/employees/{id}', [\App\Http\Controllers\Siakad\HrdController::class, 'destroy']);
        Route::get('/attendance', [\App\Http\Controllers\Siakad\HrdController::class, 'attendance']);
        Route::post('/attendance', [\App\Http\Controllers\Siakad\HrdController::class, 'markAttendance']);
        Route::get('/stats', [\App\Http\Controllers\Siakad\HrdController::class, 'stats']);
    });

    // Manajemen Kerjasama
    Route::prefix('siakad/kerjasama')->group(function () {
        Route::get('/', [\App\Http\Controllers\Siakad\KerjasamaController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Siakad\KerjasamaController::class, 'store']);
        Route::put('/{id}', [\App\Http\Controllers\Siakad\KerjasamaController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\Siakad\KerjasamaController::class, 'destroy']);
        Route::get('/stats', [\App\Http\Controllers\Siakad\KerjasamaController::class, 'stats']);
    });

    // RPL (Rekognisi Pembelajaran Lampau)
    Route::prefix('siakad/rpl')->group(function () {
        Route::get('/applications', [\App\Http\Controllers\Siakad\RplController::class, 'index']);
        Route::post('/applications', [\App\Http\Controllers\Siakad\RplController::class, 'store']);
        Route::get('/applications/{id}', [\App\Http\Controllers\Siakad\RplController::class, 'show']);
        Route::post('/applications/{id}/review', [\App\Http\Controllers\Siakad\RplController::class, 'review']);
        Route::post('/applications/{id}/documents', [\App\Http\Controllers\Siakad\RplController::class, 'uploadDocument']);
        Route::get('/stats', [\App\Http\Controllers\Siakad\RplController::class, 'stats']);
    });

    // Career Center
    Route::prefix('siakad/career')->group(function () {
        Route::get('/jobs', [\App\Http\Controllers\Siakad\CareerController::class, 'jobs']);
        Route::post('/jobs', [\App\Http\Controllers\Siakad\CareerController::class, 'storeJob']);
        Route::put('/jobs/{id}', [\App\Http\Controllers\Siakad\CareerController::class, 'updateJob']);
        Route::delete('/jobs/{id}', [\App\Http\Controllers\Siakad\CareerController::class, 'deleteJob']);
        Route::get('/jobs/{jobId}/applications', [\App\Http\Controllers\Siakad\CareerController::class, 'applications']);
        Route::post('/jobs/{jobId}/apply', [\App\Http\Controllers\Siakad\CareerController::class, 'apply']);
        Route::patch('/applications/{id}/status', [\App\Http\Controllers\Siakad\CareerController::class, 'updateApplicationStatus']);
        Route::get('/stats', [\App\Http\Controllers\Siakad\CareerController::class, 'stats']);
    });

    // CRM CAMABA
    Route::prefix('siakad/crm')->group(function () {
        Route::get('/prospects', [\App\Http\Controllers\Siakad\CrmController::class, 'index']);
        Route::post('/prospects', [\App\Http\Controllers\Siakad\CrmController::class, 'store']);
        Route::put('/prospects/{id}', [\App\Http\Controllers\Siakad\CrmController::class, 'update']);
        Route::delete('/prospects/{id}', [\App\Http\Controllers\Siakad\CrmController::class, 'destroy']);
        Route::post('/prospects/{id}/followup', [\App\Http\Controllers\Siakad\CrmController::class, 'addFollowup']);
        Route::get('/prospects/{id}/followups', [\App\Http\Controllers\Siakad\CrmController::class, 'getFollowups']);
        Route::get('/stats', [\App\Http\Controllers\Siakad\CrmController::class, 'stats']);
    });

    // PPG (Pendidikan Profesi Guru)
    Route::prefix('siakad/ppg')->group(function () {
        Route::get('/participants', [\App\Http\Controllers\Siakad\PpgController::class, 'index']);
        Route::post('/participants', [\App\Http\Controllers\Siakad\PpgController::class, 'store']);
        Route::put('/participants/{id}', [\App\Http\Controllers\Siakad\PpgController::class, 'update']);
        Route::get('/participants/{id}/activities', [\App\Http\Controllers\Siakad\PpgController::class, 'activities']);
        Route::post('/participants/{id}/activities', [\App\Http\Controllers\Siakad\PpgController::class, 'addActivity']);
        Route::get('/stats', [\App\Http\Controllers\Siakad\PpgController::class, 'stats']);
    });

    // e-Sign Integration
    Route::prefix('siakad/esign')->group(function () {
        Route::get('/config', [\App\Http\Controllers\Siakad\EsignController::class, 'config']);
        Route::post('/config', [\App\Http\Controllers\Siakad\EsignController::class, 'updateConfig']);
        Route::post('/test', [\App\Http\Controllers\Siakad\EsignController::class, 'testConnection']);
    });

    // Perpustakaan sLimS Integration
    Route::prefix('siakad/perpustakaan')->group(function () {
        Route::get('/config', [\App\Http\Controllers\Siakad\PerpustakaanController::class, 'config']);
        Route::post('/config', [\App\Http\Controllers\Siakad\PerpustakaanController::class, 'updateConfig']);
        Route::post('/test', [\App\Http\Controllers\Siakad\PerpustakaanController::class, 'testConnection']);
        Route::get('/search', [\App\Http\Controllers\Siakad\PerpustakaanController::class, 'search']);
        Route::get('/stats', [\App\Http\Controllers\Siakad\PerpustakaanController::class, 'stats']);
    });

    // Open API Token Management
    Route::prefix('siakad/api-tokens')->group(function () {
        Route::get('/', [\App\Http\Controllers\Siakad\ApiTokenController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Siakad\ApiTokenController::class, 'store']);
        Route::delete('/{id}', [\App\Http\Controllers\Siakad\ApiTokenController::class, 'destroy']);
        Route::patch('/{id}/toggle', [\App\Http\Controllers\Siakad\ApiTokenController::class, 'toggle']);
    });

    // Beasiswa & KIP-K Management
    Route::prefix('siakad/beasiswa')->group(function () {
        Route::get('/', [\App\Http\Controllers\Siakad\ScholarshipController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Siakad\ScholarshipController::class, 'store']);
        Route::get('/masters', [\App\Http\Controllers\Siakad\ScholarshipController::class, 'masters']);
        Route::post('/masters', [\App\Http\Controllers\Siakad\ScholarshipController::class, 'storeMaster']);
        Route::get('/stats', [\App\Http\Controllers\Siakad\ScholarshipController::class, 'stats']);
        Route::patch('/{id}/status', [\App\Http\Controllers\Siakad\ScholarshipController::class, 'updateStatus']);
    });
});

// PMB Public routes (no auth required - for calon mahasiswa)
Route::prefix('siakad/pmb')->group(function () {
    Route::get('/periods', [\App\Http\Controllers\Siakad\PmbController::class, 'periods']);
    Route::post('/apply/{periodId}', [\App\Http\Controllers\Siakad\PmbController::class, 'apply']);
    Route::post('/upload/{applicantId}', [\App\Http\Controllers\Siakad\PmbController::class, 'uploadDocument']);
    Route::get('/applicant/status/{regNum}', [\App\Http\Controllers\Siakad\PmbController::class, 'checkStatusPublic'])->where('regNum', '.*');
});

