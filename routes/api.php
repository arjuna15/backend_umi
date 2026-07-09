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

    // PDF Exports & Grading
    Route::get('/siakad/export/krs', [\App\Http\Controllers\SiakadController::class, 'exportKrsPdf']);
    Route::get('/siakad/export/khs', [\App\Http\Controllers\SiakadController::class, 'exportKhsPdf']);
    Route::post('/siakad/submission/{submissionId}/grade', [\App\Http\Controllers\SiakadController::class, 'gradeSubmission']);
    Route::get('/siakad/submission/{submissionId}/download', [\App\Http\Controllers\SiakadController::class, 'downloadSubmission']);
});

