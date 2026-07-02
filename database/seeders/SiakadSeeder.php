<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Billing;
use App\Models\Material;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\AttendanceRecord;
use App\Models\Submission;
use App\Models\Forum;
use App\Models\ForumReply;
use App\Models\KrsSubmission;
use App\Models\Edom;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Bap;

class SiakadSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Disable foreign keys & truncate all tables to start fresh
        Schema::disableForeignKeyConstraints();
        User::truncate();
        Course::truncate();
        Grade::truncate();
        Billing::truncate();
        Material::truncate();
        Assignment::truncate();
        Attendance::truncate();
        AttendanceRecord::truncate();
        Submission::truncate();
        Forum::truncate();
        ForumReply::truncate();
        KrsSubmission::truncate();
        Edom::truncate();
        Quiz::truncate();
        QuizQuestion::truncate();
        Bap::truncate();
        Schema::enableForeignKeyConstraints();

        // 1. Create Admins & Kaprodi
        $admin = User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@umiba.ac.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'nim_nip' => 'admin_001',
            'prodi' => 'Semua',
            'phone' => '081234567890',
            'address' => 'Kampus UMIBA Bintaro',
            'bio' => 'Administrator Utama SIAKAD UMIBA.',
            'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150',
        ]);

        $kaprodi = User::create([
            'name' => 'Kaprodi Teknik Komputer',
            'email' => 'kaprodi.komputer@umiba.ac.id',
            'password' => Hash::make('kaprodi123'),
            'role' => 'kaprodi',
            'nim_nip' => 'kaprodi_001',
            'prodi' => 'Teknik Komputer',
            'phone' => '081234567891',
            'address' => 'Kampus UMIBA Bintaro',
            'bio' => 'Ketua Program Studi Teknik Komputer UMIBA.',
            'avatar_url' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150',
        ]);

        // 2. Create 5 Dosen (Lecturers)
        $dosens = [];
        $dosenNames = [
            'Dr. Budi Santoso',
            'Prof. Rina Amelia',
            'H. Ahmad Fauzi, M.T.',
            'Dr. Eng. Syafiq Alatas',
            'Sri Hartati, M.Kom.'
        ];
        
        $dosenNips = [
            '198001012005011001',
            '197502022010012001',
            '198203032015011002',
            '198504042018011003',
            '198805052020012002'
        ];

        $dosenBios = [
            'Dosen Spesialis Pemrograman & Algoritma.',
            'Peneliti di Bidang Sistem Informasi & Data Analytics.',
            'Praktisi Jaringan Komputer & Cyber Security.',
            'Spesialis Kecerdasan Buatan & Machine Learning.',
            'Dosen Pengampu Basis Data & Desain Sistem.'
        ];

        for ($i = 0; $i < 5; $i++) {
            $dosens[] = User::create([
                'name' => $dosenNames[$i],
                'email' => strtolower(str_replace(['Dr. ', 'Prof. ', ' ', ',', '.'], ['', '', '_', '', ''], $dosenNames[$i])) . '@umiba.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'dosen',
                'nim_nip' => $dosenNips[$i],
                'prodi' => 'Teknik Komputer',
                'phone' => '08129876543' . $i,
                'address' => 'Jakarta, Indonesia',
                'bio' => $dosenBios[$i],
                'avatar_url' => 'https://images.unsplash.com/photo-' . (1500000000000 + ($i * 1000000)) . '?w=150',
            ]);
        }

        // 3. Create 30 Mahasiswa (Students)
        $students = [];
        $studentNames = [
            'Andi Pratama', 'Siti Aminah', 'Kevin Sanjaya', 'Budi Wijaya', 'Dewi Lestari',
            'Eko Prasetyo', 'Fajar Nugraha', 'Gita Rahayu', 'Hendra Wijaya', 'Indah Permata',
            'Joko Susilo', 'Kartika Sari', 'Lukman Hakim', 'Mega Utami', 'Nova Arianto',
            'Olivia Putri', 'Putra Perkasa', 'Qori Aina', 'Rian Hidayat', 'Siska Amelia',
            'Taufik Hidayat', 'Utami Lestari', 'Vicky Prasetyo', 'Wulan Dari', 'Xena Putri',
            'Yuda Pratama', 'Zhafira Aliyah', 'Aditya Nugroho', 'Bella Safira', 'Citra Kirana'
        ];

        for ($i = 0; $i < 30; $i++) {
            // Assign a dosen wali (academic advisor) from our 5 lecturers
            $dosenWali = $dosens[$i % 5];
            
            $students[] = User::create([
                'name' => $studentNames[$i],
                'email' => strtolower(str_replace(' ', '', $studentNames[$i])) . '@mhs.umiba.ac.id',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
                'nim_nip' => '2023010' . str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'prodi' => 'Teknik Komputer',
                'phone' => '0857123456' . str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'address' => 'Tangerang Selatan, Banten',
                'bio' => 'Mahasiswa Prodi Teknik Komputer UMIBA Angkatan 2023.',
                'avatar_url' => null,
                'dosen_wali_id' => $dosenWali->id,
            ]);
        }

        // 4. Create Courses (Mata Kuliah)
        $courses = [];
        $courseData = [
            ['code' => 'COMP101', 'name' => 'Algoritma dan Pemrograman', 'sks' => 3, 'dosen_id' => $dosens[0]->id, 'hari' => 'Senin', 'jam_mulai' => '08:00', 'jam_selesai' => '10:30', 'ruang' => 'Lab Komputer A'],
            ['code' => 'COMP102', 'name' => 'Struktur Data', 'sks' => 3, 'dosen_id' => $dosens[1]->id, 'hari' => 'Selasa', 'jam_mulai' => '10:40', 'jam_selesai' => '13:10', 'ruang' => 'Lab Komputer B'],
            ['code' => 'COMP201', 'name' => 'Jaringan Komputer', 'sks' => 4, 'dosen_id' => $dosens[2]->id, 'hari' => 'Rabu', 'jam_mulai' => '13:20', 'jam_selesai' => '16:40', 'ruang' => 'Ruang 401'],
            ['code' => 'COMP305', 'name' => 'Kecerdasan Buatan', 'sks' => 3, 'dosen_id' => $dosens[3]->id, 'hari' => 'Kamis', 'jam_mulai' => '08:00', 'jam_selesai' => '10:30', 'ruang' => 'Ruang 402'],
            ['code' => 'COMP402', 'name' => 'Basis Data Lanjut', 'sks' => 3, 'dosen_id' => $dosens[4]->id, 'hari' => 'Jumat', 'jam_mulai' => '10:40', 'jam_selesai' => '13:10', 'ruang' => 'Lab Komputer A'],
            ['code' => 'UM001', 'name' => 'Pendidikan Pancasila', 'sks' => 2, 'dosen_id' => $dosens[1]->id, 'hari' => 'Sabtu', 'jam_mulai' => '08:00', 'jam_selesai' => '09:40', 'ruang' => 'Ruang Seminar 1']
        ];

        foreach ($courseData as $c) {
            $courses[] = Course::create([
                'code' => $c['code'],
                'name' => $c['name'],
                'sks' => $c['sks'],
                'dosen_id' => $c['dosen_id'],
                'prodi' => 'Teknik Komputer',
                'semester' => 'Ganjil 2026/2027',
                'hari' => $c['hari'],
                'jam_mulai' => $c['jam_mulai'],
                'jam_selesai' => $c['jam_selesai'],
                'ruang' => $c['ruang']
            ]);
        }

        // 5. Enroll Students and Generate Grades
        $grades = ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'D', 'E'];
        foreach ($students as $idx => $student) {
            // First 10 students have fully graded histories
            if ($idx < 10) {
                Grade::create(['mahasiswa_id' => $student->id, 'course_id' => $courses[0]->id, 'score' => rand(80, 100), 'grade' => $grades[rand(0, 2)]]);
                Grade::create(['mahasiswa_id' => $student->id, 'course_id' => $courses[1]->id, 'score' => rand(70, 85), 'grade' => $grades[rand(2, 4)]]);
                Grade::create(['mahasiswa_id' => $student->id, 'course_id' => $courses[2]->id, 'score' => rand(60, 75), 'grade' => $grades[rand(4, 6)]]);
            } else if ($idx < 20) {
                // Students 11-20 are still pending/partially graded
                Grade::create(['mahasiswa_id' => $student->id, 'course_id' => $courses[0]->id, 'score' => rand(75, 95), 'grade' => $grades[rand(1, 3)]]);
                Grade::create(['mahasiswa_id' => $student->id, 'course_id' => $courses[1]->id, 'score' => null, 'grade' => null]);
            } else {
                // Students 21-30 are completely new
                Grade::create(['mahasiswa_id' => $student->id, 'course_id' => $courses[0]->id, 'score' => null, 'grade' => null]);
            }
        }

        // 6. Generate KRS Submissions
        // First 10 students: Approved KRS
        for ($i = 0; $i < 10; $i++) {
            KrsSubmission::create([
                'mahasiswa_id' => $students[$i]->id,
                'semester' => 'Ganjil 2026/2027',
                'course_ids' => [$courses[0]->id, $courses[1]->id, $courses[2]->id, $courses[5]->id],
                'status' => 'approved',
                'notes' => 'Rencana studi disetujui oleh dosen wali.'
            ]);
        }
        // Next 10 students: Pending KRS
        for ($i = 10; $i < 20; $i++) {
            KrsSubmission::create([
                'mahasiswa_id' => $students[$i]->id,
                'semester' => 'Ganjil 2026/2027',
                'course_ids' => [$courses[0]->id, $courses[2]->id, $courses[3]->id],
                'status' => 'pending',
                'notes' => null
            ]);
        }
        // Next 5 students: Rejected KRS
        for ($i = 20; $i < 25; $i++) {
            KrsSubmission::create([
                'mahasiswa_id' => $students[$i]->id,
                'semester' => 'Ganjil 2026/2027',
                'course_ids' => [$courses[1]->id, $courses[4]->id],
                'status' => 'rejected',
                'notes' => 'SKS melebihi batas IPS semester lalu.'
            ]);
        }

        // 7. Create Billings
        foreach ($students as $idx => $student) {
            $billingStatus = ($idx % 3 === 0) ? 'Belum Lunas' : 'Lunas';
            Billing::create([
                'user_id' => $student->id,
                'description' => 'UKT Semester Ganjil 2026/2027',
                'amount' => 4500000,
                'status' => $billingStatus,
                'due_date' => '2026-08-30'
            ]);

            if ($idx % 5 === 0) {
                Billing::create([
                    'user_id' => $student->id,
                    'description' => 'Sumbangan Pembangunan Mahasiswa Baru',
                    'amount' => 1500000,
                    'status' => 'Lunas',
                    'due_date' => '2026-07-01'
                ]);
            }
        }

        // 8. E-Learning Materials & Assignments
        foreach ($courses as $c) {
            Material::create([
                'course_id' => $c->id,
                'title' => 'Pertemuan 1: Kontrak Kuliah & Pengenalan Umum (PDF)',
                'content_link' => 'https://example.com/materi_pertemuan1.pdf'
            ]);
            Material::create([
                'course_id' => $c->id,
                'title' => 'Pertemuan 2: Konsep Dasar Teori & Implementasi Sederhana (Slide)',
                'content_link' => 'https://example.com/materi_pertemuan2.pdf'
            ]);

            $assignment = Assignment::create([
                'course_id' => $c->id,
                'title' => 'Tugas Mandiri 1: Pembuatan Laporan Analisis',
                'description' => 'Susun laporan ringkas mengenai studi kasus pertemuan 1 & 2. Kumpulkan dalam format PDF.',
                'deadline' => '2026-09-15'
            ]);

            // Create submissions for the first 5 students in each course
            for ($i = 0; $i < 5; $i++) {
                $gradedScore = ($i % 2 === 0) ? rand(80, 95) : null;
                Submission::create([
                    'assignment_id' => $assignment->id,
                    'mahasiswa_id' => $students[$i]->id,
                    'file_path' => 'submissions/tugas1_' . strtolower(str_replace(' ', '', $students[$i]->name)) . '.pdf',
                    'grade' => $gradedScore
                ]);
            }
        }

        // 9. Forums & Replies
        foreach ($courses as $idx => $c) {
            $forum = Forum::create([
                'course_id' => $c->id,
                'user_id' => $c->dosen_id,
                'title' => 'Diskusi Terbuka Pertemuan 1 - ' . $c->name,
                'content' => 'Gunakan thread ini untuk mendiskusikan materi pertama kita.'
            ]);

            ForumReply::create([
                'forum_id' => $forum->id,
                'user_id' => $students[0]->id,
                'content' => 'Mohon izin bertanya, apakah ada batas pengumpulan tugas mandiri 1?'
            ]);

            ForumReply::create([
                'forum_id' => $forum->id,
                'user_id' => $c->dosen_id,
                'content' => 'Batas pengumpulan sesuai yang tercantum di sistem e-learning (15 September 2026).'
            ]);
        }

        // 10. Attendances & Records
        foreach ($courses as $c) {
            for ($meeting = 1; $meeting <= 4; $meeting++) {
                $attendance = Attendance::create([
                    'course_id' => $c->id,
                    'meeting_number' => $meeting,
                    'date' => date('Y-m-d', strtotime("-$meeting weeks")),
                    'mode' => ($meeting % 2 === 0) ? 'online' : 'offline'
                ]);

                // Create record for each student enrolled (let's say all 30 students)
                foreach ($students as $idx => $student) {
                    $statuses = ['present', 'present', 'present', 'present', 'present', 'present', 'present', 'absent', 'late', 'excused'];
                    $status = $statuses[($idx + $meeting) % count($statuses)];
                    AttendanceRecord::create([
                        'attendance_id' => $attendance->id,
                        'mahasiswa_id' => $student->id,
                        'status' => $status
                    ]);
                }

                // Create BAP (Berita Acara Perkuliahan) for this meeting
                Bap::create([
                    'course_id' => $c->id,
                    'dosen_id' => $c->dosen_id,
                    'meeting_number' => $meeting,
                    'date' => $attendance->date,
                    'topic' => 'Topik Pembahasan Sesi ke-' . $meeting . ' - ' . $c->name,
                    'notes' => 'Perkuliahan berjalan kondusif. Evaluasi diskusi aktif.'
                ]);
            }
        }

        // 11. EDOM (Evaluasi Dosen Oleh Mahasiswa)
        for ($i = 0; $i < 15; $i++) {
            $student = $students[$i];
            foreach ($courses as $c) {
                Edom::create([
                    'dosen_id' => $c->dosen_id,
                    'mahasiswa_id' => $student->id,
                    'course_id' => $c->id,
                    'score' => rand(4, 5),
                    'comment' => 'Penyampaian materi perkuliahan sangat jelas dan mudah dipahami.'
                ]);
            }
        }

        // 12. Quizzes & Questions
        foreach ($courses as $c) {
            $quiz = Quiz::create([
                'course_id' => $c->id,
                'title' => 'Kuis Evaluasi Bab 1 - ' . $c->name,
                'duration_minutes' => 30,
                'randomize_questions' => true
            ]);

            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => 'Apa definisi dasar dari pokok bahasan materi utama kita?',
                'option_a' => 'Pokok Bahasan A',
                'option_b' => 'Pokok Bahasan B',
                'option_c' => 'Pokok Bahasan C',
                'option_d' => 'Pokok Bahasan D',
                'correct_answer' => 'a',
                'type' => 'multiple_choice',
                'correct_answer_text' => null
            ]);

            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => 'Jelaskan implementasi nyata konsep bab 1 di kehidupan sehari-hari.',
                'option_a' => '',
                'option_b' => '',
                'option_c' => '',
                'option_d' => '',
                'correct_answer' => '',
                'type' => 'essay',
                'correct_answer_text' => 'Jawaban esai yang dinilai manual.'
            ]);
        }
    }
}
