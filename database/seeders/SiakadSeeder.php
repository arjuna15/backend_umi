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
    }
}
