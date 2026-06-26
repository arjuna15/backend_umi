<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Course;
use App\Models\Grade;

class SiakadSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Create Admin and Kaprodi
        $admin = User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@umiba.ac.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'nim_nip' => 'admin_001',
            'prodi' => 'Semua',
        ]);

        $kaprodi = User::create([
            'name' => 'Kaprodi Teknik Komputer',
            'email' => 'kaprodi.komputer@umiba.ac.id',
            'password' => Hash::make('kaprodi123'),
            'role' => 'kaprodi',
            'nim_nip' => 'kaprodi_001',
            'prodi' => 'Teknik Komputer',
        ]);

        // 1. Create Dosen
        $dosen1 = User::create([
            'name' => 'Dr. Budi Santoso',
            'email' => 'budi@umiba.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'dosen',
            'nim_nip' => '198001012005011001',
            'prodi' => 'Teknik Komputer',
        ]);
        
        $dosen2 = User::create([
            'name' => 'Prof. Rina Amelia',
            'email' => 'rina@umiba.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'dosen',
            'nim_nip' => '197502022010012001',
            'prodi' => 'Sistem Informasi',
        ]);

        // 2. Create Mahasiswa
        $mahasiswa1 = User::create([
            'name' => 'Andi Pratama',
            'email' => 'andi@mhs.umiba.ac.id',
            'password' => Hash::make('mahasiswa123'),
            'role' => 'mahasiswa',
            'nim_nip' => '202301001',
            'prodi' => 'Teknik Komputer',
        ]);

        $mahasiswa2 = User::create([
            'name' => 'Siti Aminah',
            'email' => 'siti@mhs.umiba.ac.id',
            'password' => Hash::make('mahasiswa123'),
            'role' => 'mahasiswa',
            'nim_nip' => '202301002',
            'prodi' => 'Teknik Komputer',
        ]);
        
        $mahasiswa3 = User::create([
            'name' => 'Kevin Sanjaya',
            'email' => 'kevin@mhs.umiba.ac.id',
            'password' => Hash::make('mahasiswa123'),
            'role' => 'mahasiswa',
            'nim_nip' => '202301003',
            'prodi' => 'Teknik Komputer',
        ]);

        // 3. Create Courses
        $course1 = Course::create([
            'code' => 'COMP101',
            'name' => 'Algoritma dan Pemrograman',
            'sks' => 3,
            'dosen_id' => $dosen1->id,
            'prodi' => 'Teknik Komputer',
            'semester' => 'Ganjil 2026/2027',
        ]);

        $course2 = Course::create([
            'code' => 'COMP102',
            'name' => 'Struktur Data',
            'sks' => 3,
            'dosen_id' => $dosen1->id,
            'prodi' => 'Teknik Komputer',
            'semester' => 'Ganjil 2026/2027',
        ]);
        
        $course3 = Course::create([
            'code' => 'COMP201',
            'name' => 'Jaringan Komputer',
            'sks' => 4,
            'dosen_id' => $dosen2->id,
            'prodi' => 'Teknik Komputer',
            'semester' => 'Ganjil 2026/2027',
        ]);
        
        $course4 = Course::create([
            'code' => 'COMP305',
            'name' => 'Kecerdasan Buatan',
            'sks' => 3,
            'dosen_id' => $dosen1->id,
            'prodi' => 'Teknik Komputer',
            'semester' => 'Genap 2025/2026',
        ]);
        
        $course5 = Course::create([
            'code' => 'UM001',
            'name' => 'Pendidikan Pancasila',
            'sks' => 2,
            'dosen_id' => $dosen2->id,
            'prodi' => 'Umum',
            'semester' => 'Ganjil 2026/2027',
        ]);

        // 4. Enroll Students (Grades)
        // Andi (mahasiswa1)
        Grade::create(['mahasiswa_id' => $mahasiswa1->id, 'course_id' => $course1->id, 'score' => 85.5, 'grade' => 'A']);
        Grade::create(['mahasiswa_id' => $mahasiswa1->id, 'course_id' => $course2->id, 'score' => 78.0, 'grade' => 'B+']);
        Grade::create(['mahasiswa_id' => $mahasiswa1->id, 'course_id' => $course3->id, 'score' => null, 'grade' => null]);
        Grade::create(['mahasiswa_id' => $mahasiswa1->id, 'course_id' => $course4->id, 'score' => 92.0, 'grade' => 'A']);
        Grade::create(['mahasiswa_id' => $mahasiswa1->id, 'course_id' => $course5->id, 'score' => 88.0, 'grade' => 'A-']);

        // Siti (mahasiswa2)
        Grade::create(['mahasiswa_id' => $mahasiswa2->id, 'course_id' => $course1->id, 'score' => 70.0, 'grade' => 'B']);
        Grade::create(['mahasiswa_id' => $mahasiswa2->id, 'course_id' => $course2->id, 'score' => 95.0, 'grade' => 'A']);
        Grade::create(['mahasiswa_id' => $mahasiswa2->id, 'course_id' => $course3->id, 'score' => null, 'grade' => null]);

        // Kevin (mahasiswa3)
        Grade::create(['mahasiswa_id' => $mahasiswa3->id, 'course_id' => $course1->id, 'score' => 65.0, 'grade' => 'C+']);
        Grade::create(['mahasiswa_id' => $mahasiswa3->id, 'course_id' => $course5->id, 'score' => null, 'grade' => null]);

        // 5. Create Billings
        \App\Models\Billing::create(['user_id' => $mahasiswa1->id, 'description' => 'UKT Semester Ganjil 2026/2027', 'amount' => 4500000, 'status' => 'Belum Lunas', 'due_date' => '2026-08-30']);
        \App\Models\Billing::create(['user_id' => $mahasiswa1->id, 'description' => 'Sumbangan Pembangunan', 'amount' => 1500000, 'status' => 'Lunas', 'due_date' => '2026-07-01']);
        
        \App\Models\Billing::create(['user_id' => $mahasiswa2->id, 'description' => 'UKT Semester Ganjil 2026/2027', 'amount' => 4500000, 'status' => 'Lunas', 'due_date' => '2026-08-30']);
        \App\Models\Billing::create(['user_id' => $mahasiswa3->id, 'description' => 'UKT Semester Ganjil 2026/2027', 'amount' => 4500000, 'status' => 'Belum Lunas', 'due_date' => '2026-08-30']);

        // 6. Create E-Learning Materials & Assignments
        \App\Models\Material::create(['course_id' => $course1->id, 'title' => 'Pertemuan 1: Pengenalan Algoritma (PDF)', 'content_link' => 'https://example.com/materi1.pdf']);
        \App\Models\Material::create(['course_id' => $course1->id, 'title' => 'Pertemuan 2: Variabel & Tipe Data (Slide)', 'content_link' => 'https://example.com/materi2.pdf']);
        
        $assignment1 = \App\Models\Assignment::create(['course_id' => $course1->id, 'title' => 'Tugas 1: Membuat Flowchart', 'description' => 'Buat flowchart untuk menentukan bilangan genap dan ganjil.', 'deadline' => '2026-09-10']);

        // 7. Create Attendances
        $attendance1 = \App\Models\Attendance::create(['course_id' => $course1->id, 'meeting_number' => 1, 'date' => '2026-09-01']);
        \App\Models\AttendanceRecord::create(['attendance_id' => $attendance1->id, 'mahasiswa_id' => $mahasiswa1->id, 'status' => 'present']);
        \App\Models\AttendanceRecord::create(['attendance_id' => $attendance1->id, 'mahasiswa_id' => $mahasiswa2->id, 'status' => 'present']);
        \App\Models\AttendanceRecord::create(['attendance_id' => $attendance1->id, 'mahasiswa_id' => $mahasiswa3->id, 'status' => 'absent']);

        // 8. Create Submissions
        \App\Models\Submission::create(['assignment_id' => $assignment1->id, 'mahasiswa_id' => $mahasiswa1->id, 'file_path' => 'submissions/andi_tugas1.pdf', 'grade' => 90]);
        \App\Models\Submission::create(['assignment_id' => $assignment1->id, 'mahasiswa_id' => $mahasiswa2->id, 'file_path' => 'submissions/siti_tugas1.pdf', 'grade' => null]);

        // 9. Create Forums
        $forum1 = \App\Models\Forum::create(['course_id' => $course1->id, 'user_id' => $dosen1->id, 'title' => 'Diskusi Materi 1', 'content' => 'Silahkan bertanya jika ada yang kurang jelas dari materi 1.']);
        \App\Models\ForumReply::create(['forum_id' => $forum1->id, 'user_id' => $mahasiswa1->id, 'content' => 'Pak, untuk tugas 1 apakah boleh pakai Visio?']);
        \App\Models\ForumReply::create(['forum_id' => $forum1->id, 'user_id' => $dosen1->id, 'content' => 'Boleh, yang penting disave ke PDF ya.']);
    }
}
