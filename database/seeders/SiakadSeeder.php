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
        $dosen = User::create([
            'name' => 'Dr. Budi Santoso',
            'email' => 'budi@umiba.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'dosen',
            'nim_nip' => '198001012005011001',
            'prodi' => 'Teknik Komputer',
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

        // 3. Create Courses
        $course1 = Course::create([
            'code' => 'COMP101',
            'name' => 'Algoritma dan Pemrograman',
            'sks' => 3,
            'dosen_id' => $dosen->id,
            'prodi' => 'Teknik Komputer',
            'semester' => 'Ganjil 2026/2027',
        ]);

        $course2 = Course::create([
            'code' => 'COMP102',
            'name' => 'Struktur Data',
            'sks' => 3,
            'dosen_id' => $dosen->id,
            'prodi' => 'Teknik Komputer',
            'semester' => 'Ganjil 2026/2027',
        ]);

        // 4. Enroll Students (Grades)
        Grade::create([
            'mahasiswa_id' => $mahasiswa1->id,
            'course_id' => $course1->id,
            'score' => 85.5,
            'grade' => 'A',
        ]);
        
        Grade::create([
            'mahasiswa_id' => $mahasiswa1->id,
            'course_id' => $course2->id,
            'score' => null,
            'grade' => null, // Belum keluar nilainya
        ]);
        
        Grade::create([
            'mahasiswa_id' => $mahasiswa2->id,
            'course_id' => $course1->id,
            'score' => 78.0,
            'grade' => 'B+',
        ]);
    }
}
