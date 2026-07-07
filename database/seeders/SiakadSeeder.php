<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SiakadSeeder extends Seeder
{
    private function normalize(?string $value): string
    {
        return Str::of((string) $value)->trim()->squish()->title()->toString();
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
            $score >= 50 => 'D',
            default => 'E',
        };
    }

    private function withTimestamps(array $row, string $now): array
    {
        $row['created_at'] = $now;
        $row['updated_at'] = $now;
        return $row;
    }

    private function insertRows(string $table, array $rows, string $now): void
    {
        if (empty($rows)) {
            return;
        }

        DB::table($table)->insert(array_map(function (array $row) use ($now) {
            return $this->withTimestamps($row, $now);
        }, $rows));
    }

    public function run(): void
    {
        $now = now()->toDateTimeString();

        Schema::disableForeignKeyConstraints();
        foreach ([
            'attendance_records',
            'attendances',
            'consultation_messages',
            'submissions',
            'forum_replies',
            'forums',
            'quiz_questions',
            'quizzes',
            'materials',
            'assignments',
            'grades',
            'krs_submissions',
            'billings',
            'edoms',
            'baps',
            'courses',
            'study_programs',
            'classrooms',
            'academic_calendars',
            'activity_logs',
            'letter_requests',
            'users',
        ] as $table) {
            DB::table($table)->truncate();
        }
        Schema::enableForeignKeyConstraints();

        $seedBase = database_path('seed-data/pddikti');
        $dosenFile = $seedBase . '/dosen.json';
        $mahasiswaFile = $seedBase . '/mahasiswa.json';

        if (!file_exists($dosenFile) || !file_exists($mahasiswaFile)) {
            $this->command?->warn('PDDikti seed files not found. Skipping SIAKAD seed.');
            return;
        }

        $dosenData = json_decode(file_get_contents($dosenFile), true) ?: [];
        $mahasiswaData = json_decode(file_get_contents($mahasiswaFile), true) ?: [];

        $uniqByKey = static function (array $rows, callable $keyResolver): array {
            $seen = [];
            $out = [];
            foreach ($rows as $row) {
                $key = trim((string) $keyResolver($row));
                if ($key === '' || isset($seen[$key])) {
                    continue;
                }
                $seen[$key] = true;
                $out[] = $row;
            }
            return $out;
        };

        $slugifyEmail = static function (string $name, string $suffix): string {
            $base = Str::slug($name);
            if ($base === '') {
                $base = 'user';
            }
            return $base . $suffix;
        };

        $dosenData = $uniqByKey($dosenData, static function (array $row): string {
            return trim((string) ($row['nidn'] ?? $row['nuptk'] ?? $row['id'] ?? ''));
        });
        $mahasiswaData = $uniqByKey($mahasiswaData, static function (array $row): string {
            return trim((string) ($row['nim'] ?? $row['id'] ?? ''));
        });

        $admin = User::create([
            'name' => 'Admin UMIBA',
            'email' => 'admin@umiba.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'nim_nip' => 'admin_001',
            'prodi' => 'Semua',
            'status' => 'Aktif',
            'bio' => 'Akun administrasi sistem SIAKAD UMIBA.',
        ]);

        $prodiLeadLecturer = [];
        $kaprodiAssigned = [];
        $dosenUsers = [];
        foreach ($dosenData as $index => $row) {
            $name = $this->normalize($row['nama'] ?? 'Dosen ' . ($index + 1));
            $prodi = $this->normalize($row['nama_prodi'] ?? '-');
            $baseId = trim((string) ($row['nidn'] ?? '')) !== '' ? trim((string) $row['nidn']) : trim((string) ($row['nuptk'] ?? ''));
            $nimNip = $baseId !== '' ? $baseId : 'DOSEN' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);
            $isKaprodi = !isset($kaprodiAssigned[$prodi]);
            $role = $isKaprodi ? 'kaprodi' : 'dosen';
            if ($isKaprodi) {
                $kaprodiAssigned[$prodi] = true;
            }
            if ($prodi !== '-' && !isset($prodiLeadLecturer[$prodi])) {
                $prodiLeadLecturer[$prodi] = $name;
            }

            $jfas = ['Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Guru Besar'];
            $randomJfa = $isKaprodi ? 'Lektor Kepala' : $jfas[array_rand($jfas)];

            $dosenUsers[] = User::create([
                'name' => $name,
                'email' => $slugifyEmail($name, '.' . strtolower($role) . '.' . $nimNip . '@umiba.ac.id'),
                'password' => Hash::make('password123'),
                'role' => $role,
                'nim_nip' => $nimNip,
                'prodi' => $prodi,
                'status' => 'Aktif',
                'jfa' => $randomJfa,
                'phone' => '08' . rand(11, 19) . rand(10000000, 99999999),
                'address' => 'Jl. UMIBA Raya No. ' . rand(1, 99) . ', Jakarta Selatan',
                'bio' => $role === 'kaprodi'
                    ? 'Ketua program studi ' . $prodi . ' UMIBA. Spesialisasi penelitian di bidang terkait.'
                    : 'Dosen tetap Universitas Mitra Bangsa (UMIBA) pada program studi ' . $prodi . '.',
            ]);
        }

        $mahasiswaUsers = [];
        foreach ($mahasiswaData as $index => $row) {
            $name = $this->normalize($row['nama'] ?? 'Mahasiswa ' . ($index + 1));
            $nim = trim((string) ($row['nim'] ?? ''));
            if ($nim === '') {
                $nim = 'MHS' . str_pad((string) ($index + 1), 6, '0', STR_PAD_LEFT);
            }
            $prodi = $this->normalize($row['nama_prodi'] ?? '-');
            $statusSaatIni = (string) ($row['status_saat_ini'] ?? 'Aktif');
            $status = 'Aktif';
            if ($statusSaatIni !== '') {
                $lower = Str::lower($statusSaatIni);
                if (Str::contains($lower, ['lulus', 'graduate', 'yudisium'])) {
                    $status = 'Lulus';
                } elseif (Str::contains($lower, ['keluar', 'drop', 'nonaktif'])) {
                    $status = 'Keluar';
                }
            }

            $advisor = null;
            foreach ($dosenUsers as $dosen) {
                if ($dosen->prodi === $prodi) {
                    $advisor = $dosen;
                    break;
                }
            }
            $advisor ??= $dosenUsers[$index % max(1, count($dosenUsers))] ?? null;

            $mahasiswaUsers[] = User::create([
                'name' => $name,
                'email' => $slugifyEmail($name, '.mhs.' . $nim . '@umiba.ac.id'),
                'password' => Hash::make('password123'),
                'role' => 'mahasiswa',
                'nim_nip' => $nim,
                'prodi' => $prodi,
                'status' => $status,
                'dosen_wali_id' => $advisor?->id,
                'phone' => '08' . rand(52, 59) . rand(10000000, 99999999),
                'address' => 'Jl. Kemanggisan No. ' . rand(1, 200) . ', Jakarta Barat',
                'bio' => 'Mahasiswa Universitas Mitra Bangsa (UMIBA) angkatan ' . rand(2022, 2025) . ' program studi ' . $prodi . '.',
            ]);
        }

        $prodiDefinitions = [
            'Ilmu Komputer' => ['code' => 'IK', 'jenjang' => 'S1'],
            'Ilmu Aktuaria' => ['code' => 'AK', 'jenjang' => 'S1'],
            'Sistem Dan Teknologi Informasi' => ['code' => 'STI', 'jenjang' => 'S1'],
            'Hukum' => ['code' => 'HK', 'jenjang' => 'S1'],
            'Manajemen' => ['code' => 'MN', 'jenjang' => 'S1'],
        ];

        $studyProgramRows = [];
        foreach ($prodiDefinitions as $name => $meta) {
            $studyProgramRows[] = [
                'code' => $meta['code'],
                'name' => $name,
                'kaprodi' => $prodiLeadLecturer[$name] ?? null,
                'jenjang' => $meta['jenjang'],
            ];
        }
        $this->insertRows('study_programs', $studyProgramRows, $now);

        $classroomRows = [
            ['code' => 'LAB-KOM-01', 'name' => 'Lab Komputer A', 'capacity' => 32, 'type' => 'Laboratorium'],
            ['code' => 'LAB-KOM-02', 'name' => 'Lab Komputer B', 'capacity' => 30, 'type' => 'Laboratorium'],
            ['code' => 'LAB-JARKOM', 'name' => 'Lab Jaringan', 'capacity' => 28, 'type' => 'Laboratorium'],
            ['code' => 'R-401', 'name' => 'Ruang 401', 'capacity' => 45, 'type' => 'Teori'],
            ['code' => 'R-402', 'name' => 'Ruang 402', 'capacity' => 42, 'type' => 'Teori'],
            ['code' => 'R-403', 'name' => 'Ruang 403', 'capacity' => 40, 'type' => 'Teori'],
        ];
        $this->insertRows('classrooms', $classroomRows, $now);

        $lecturersByProdi = [];
        foreach ($dosenUsers as $lecturer) {
            $lecturersByProdi[$lecturer->prodi][] = $lecturer;
        }

                $catalog = [
            'Ilmu Komputer' => [
                1 => [
                    ['code' => 'IK-101', 'name' => 'Dasar Pemrograman', 'sks' => 3],
                    ['code' => 'IK-103', 'name' => 'Pengantar Teknologi Informasi', 'sks' => 3],
                    ['code' => 'IK-105', 'name' => 'Matematika Diskrit', 'sks' => 3],
                    ['code' => 'IK-107', 'name' => 'Kalkulus 1', 'sks' => 3],
                    ['code' => 'IK-109', 'name' => 'Bahasa Inggris Akademik', 'sks' => 2],
                    ['code' => 'IK-111', 'name' => 'Pendidikan Kewarganegaraan', 'sks' => 2],
                ],
                2 => [
                    ['code' => 'IK-202', 'name' => 'Struktur Data & Algoritma', 'sks' => 3],
                    ['code' => 'IK-204', 'name' => 'Sistem Operasi', 'sks' => 3],
                    ['code' => 'IK-206', 'name' => 'Basis Data', 'sks' => 3],
                    ['code' => 'IK-208', 'name' => 'Arsitektur Komputer', 'sks' => 3],
                    ['code' => 'IK-210', 'name' => 'Kalkulus 2', 'sks' => 3],
                    ['code' => 'IK-212', 'name' => 'Statistika & Probabilitas', 'sks' => 3],
                ],
                3 => [
                    ['code' => 'IK-301', 'name' => 'Pemrograman Berorientasi Objek', 'sks' => 3],
                    ['code' => 'IK-303', 'name' => 'Jaringan Komputer', 'sks' => 3],
                    ['code' => 'IK-305', 'name' => 'Analisis Algoritma', 'sks' => 3],
                    ['code' => 'IK-307', 'name' => 'Aljabar Linear', 'sks' => 3],
                ],
                4 => [
                    ['code' => 'IK-402', 'name' => 'Kecerdasan Buatan', 'sks' => 3],
                    ['code' => 'IK-404', 'name' => 'Pemrograman Web', 'sks' => 3],
                    ['code' => 'IK-406', 'name' => 'Sistem Informasi', 'sks' => 3],
                ],
                5 => [
                    ['code' => 'IK-501', 'name' => 'Pemrograman Web Lanjut', 'sks' => 3],
                    ['code' => 'IK-503', 'name' => 'Rekayasa Perangkat Lunak', 'sks' => 3],
                    ['code' => 'IK-505', 'name' => 'Kecerdasan Buatan Lanjut', 'sks' => 3],
                ],
                6 => [
                    ['code' => 'IK-602', 'name' => 'Pembelajaran Mesin (Machine Learning)', 'sks' => 3],
                    ['code' => 'IK-604', 'name' => 'Komputasi Awan (Cloud Computing)', 'sks' => 3],
                    ['code' => 'IK-606', 'name' => 'Interaksi Manusia & Komputer', 'sks' => 3],
                ],
                7 => [
                    ['code' => 'IK-701', 'name' => 'Metodologi Penelitian', 'sks' => 2],
                    ['code' => 'IK-703', 'name' => 'Kuliah Kerja Nyata (KKN)', 'sks' => 3],
                    ['code' => 'IK-705', 'name' => 'Magang Industri', 'sks' => 4],
                ],
                8 => [
                    ['code' => 'IK-802', 'name' => 'Skripsi / Tugas Akhir', 'sks' => 6],
                ]
            ],
            'Sistem Dan Teknologi Informasi' => [
                1 => [
                    ['code' => 'STI-101', 'name' => 'Pengantar Sistem Informasi', 'sks' => 3],
                    ['code' => 'STI-103', 'name' => 'Algoritma & Pemrograman Dasar', 'sks' => 3],
                    ['code' => 'STI-105', 'name' => 'Matematika Bisnis', 'sks' => 3],
                    ['code' => 'STI-107', 'name' => 'English for IT', 'sks' => 2],
                    ['code' => 'STI-109', 'name' => 'Pancasila & Kewarganegaraan', 'sks' => 2],
                ],
                2 => [
                    ['code' => 'STI-202', 'name' => 'Desain Proses Bisnis', 'sks' => 3],
                    ['code' => 'STI-204', 'name' => 'Sistem Operasi & Jaringan', 'sks' => 3],
                    ['code' => 'STI-206', 'name' => 'Konsep Basis Data', 'sks' => 3],
                    ['code' => 'STI-208', 'name' => 'Algoritma & Struktur Data', 'sks' => 3],
                    ['code' => 'STI-210', 'name' => 'Statistika Deskriptif', 'sks' => 3],
                ],
                3 => [
                    ['code' => 'STI-301', 'name' => 'Analisis Sistem Informasi', 'sks' => 3],
                    ['code' => 'STI-303', 'name' => 'Arsitektur Enterprise', 'sks' => 3],
                    ['code' => 'STI-305', 'name' => 'E-Business', 'sks' => 3],
                ],
                4 => [
                    ['code' => 'STI-402', 'name' => 'Keamanan Sistem Informasi', 'sks' => 3],
                    ['code' => 'STI-404', 'name' => 'Sistem Pendukung Keputusan', 'sks' => 3],
                    ['code' => 'STI-406', 'name' => 'Interaksi Manusia & Komputer', 'sks' => 3],
                ],
                5 => [
                    ['code' => 'STI-501', 'name' => 'Analisis Sistem Informasi Lanjut', 'sks' => 3],
                    ['code' => 'STI-503', 'name' => 'Manajemen Infrastruktur TI', 'sks' => 3],
                    ['code' => 'STI-505', 'name' => 'Manajemen Proyek TI', 'sks' => 3],
                ],
                6 => [
                    ['code' => 'STI-602', 'name' => 'Audit Sistem Informasi', 'sks' => 3],
                    ['code' => 'STI-604', 'name' => 'Tata Kelola TI', 'sks' => 3],
                    ['code' => 'STI-606', 'name' => 'Data Warehouse & Business Intelligence', 'sks' => 3],
                ],
                7 => [
                    ['code' => 'STI-701', 'name' => 'Metodologi Penelitian TI', 'sks' => 2],
                    ['code' => 'STI-703', 'name' => 'Kuliah Kerja Nyata (KKN)', 'sks' => 3],
                    ['code' => 'STI-705', 'name' => 'Magang Kerja', 'sks' => 4],
                ],
                8 => [
                    ['code' => 'STI-802', 'name' => 'Tugas Akhir / Skripsi', 'sks' => 6],
                ]
            ],
            'Ilmu Aktuaria' => [
                1 => [
                    ['code' => 'AK-101', 'name' => 'Pengantar Ilmu Aktuaria', 'sks' => 3],
                    ['code' => 'AK-103', 'name' => 'Kalkulus Aktuaria 1', 'sks' => 3],
                    ['code' => 'AK-105', 'name' => 'Mikroekonomi Dasar', 'sks' => 3],
                    ['code' => 'AK-107', 'name' => 'Pengantar Statistik', 'sks' => 3],
                    ['code' => 'AK-109', 'name' => 'Bahasa Inggris', 'sks' => 2],
                ],
                2 => [
                    ['code' => 'AK-202', 'name' => 'Matematika Finansial', 'sks' => 3],
                    ['code' => 'AK-204', 'name' => 'Makroekonomi Dasar', 'sks' => 3],
                    ['code' => 'AK-206', 'name' => 'Probabilitas & Statistika', 'sks' => 3],
                    ['code' => 'AK-208', 'name' => 'Kalkulus Aktuaria 2', 'sks' => 3],
                    ['code' => 'AK-210', 'name' => 'Dasar Pemrograman Komputer', 'sks' => 3],
                ],
                3 => [
                    ['code' => 'AK-301', 'name' => 'Matematika Finansial Lanjut', 'sks' => 3],
                    ['code' => 'AK-303', 'name' => 'Teori Suku Bunga', 'sks' => 3],
                    ['code' => 'AK-305', 'name' => 'Analisis Regresi', 'sks' => 3],
                ],
                4 => [
                    ['code' => 'AK-402', 'name' => 'Matematika Dana Pensiun', 'sks' => 3],
                    ['code' => 'AK-404', 'name' => 'Hukum Asuransi', 'sks' => 2],
                    ['code' => 'AK-406', 'name' => 'Analisis Runtun Waktu', 'sks' => 3],
                ],
                5 => [
                    ['code' => 'AK-501', 'name' => 'Matematika Aktuaria Jiwa 1', 'sks' => 3],
                    ['code' => 'AK-503', 'name' => 'Pemodelan Risiko dan Asuransi', 'sks' => 3],
                    ['code' => 'AK-505', 'name' => 'Statistika Finansial', 'sks' => 3],
                ],
                6 => [
                    ['code' => 'AK-602', 'name' => 'Matematika Aktuaria Jiwa 2', 'sks' => 3],
                    ['code' => 'AK-604', 'name' => 'Teori Risiko Kredibilitas', 'sks' => 3],
                    ['code' => 'AK-606', 'name' => 'Manajemen Risiko Perusahaan', 'sks' => 3],
                ],
                7 => [
                    ['code' => 'AK-701', 'name' => 'Metodologi Riset Aktuaria', 'sks' => 2],
                    ['code' => 'AK-703', 'name' => 'Kuliah Kerja Nyata (KKN)', 'sks' => 3],
                    ['code' => 'AK-705', 'name' => 'Magang Profesi Aktuaria', 'sks' => 4],
                ],
                8 => [
                    ['code' => 'AK-802', 'name' => 'Tugas Akhir / Skripsi', 'sks' => 6],
                ]
            ],
            'Hukum' => [
                1 => [
                    ['code' => 'HK-101', 'name' => 'Pengantar Ilmu Hukum', 'sks' => 4],
                    ['code' => 'HK-103', 'name' => 'Pengantar Hukum Indonesia', 'sks' => 4],
                    ['code' => 'HK-105', 'name' => 'Hukum Adat', 'sks' => 3],
                    ['code' => 'HK-107', 'name' => 'Bahasa Inggris Hukum', 'sks' => 2],
                    ['code' => 'HK-109', 'name' => 'Pancasila & Etika Hukum', 'sks' => 2],
                ],
                2 => [
                    ['code' => 'HK-202', 'name' => 'Hukum Perdata', 'sks' => 4],
                    ['code' => 'HK-204', 'name' => 'Hukum Pidana', 'sks' => 4],
                    ['code' => 'HK-206', 'name' => 'Hukum Tata Negara', 'sks' => 4],
                    ['code' => 'HK-208', 'name' => 'Hukum Islam', 'sks' => 3],
                    ['code' => 'HK-210', 'name' => 'Hukum Administrasi Negara', 'sks' => 3],
                ],
                3 => [
                    ['code' => 'HK-301', 'name' => 'Hukum Perjanjian', 'sks' => 3],
                    ['code' => 'HK-303', 'name' => 'Hukum Perburuhan', 'sks' => 3],
                    ['code' => 'HK-305', 'name' => 'Hukum Agraria', 'sks' => 3],
                ],
                4 => [
                    ['code' => 'HK-402', 'name' => 'Hukum Acara Perdata', 'sks' => 4],
                    ['code' => 'HK-404', 'name' => 'Hukum Acara Pidana', 'sks' => 4],
                    ['code' => 'HK-406', 'name' => 'Hukum Lingkungan', 'sks' => 3],
                ],
                5 => [
                    ['code' => 'HK-501', 'name' => 'Hukum Dagang & Bisnis', 'sks' => 3],
                    ['code' => 'HK-503', 'name' => 'Hukum Internasional', 'sks' => 3],
                    ['code' => 'HK-505', 'name' => 'Hukum Perlindungan Konsumen', 'sks' => 3],
                ],
                6 => [
                    ['code' => 'HK-602', 'name' => 'Hukum Kekayaan Intelektual', 'sks' => 3],
                    ['code' => 'HK-604', 'name' => 'Etika Profesi Hukum', 'sks' => 2],
                    ['code' => 'HK-606', 'name' => 'Alternatif Penyelesaian Sengketa', 'sks' => 3],
                ],
                7 => [
                    ['code' => 'HK-701', 'name' => 'Penyusunan Kontrak (Contract Drafting)', 'sks' => 3],
                    ['code' => 'HK-703', 'name' => 'Kuliah Kerja Nyata (KKN)', 'sks' => 3],
                    ['code' => 'HK-705', 'name' => 'Praktik Peradilan Semu', 'sks' => 4],
                ],
                8 => [
                    ['code' => 'HK-802', 'name' => 'Skripsi / Tugas Akhir Hukum', 'sks' => 6],
                ]
            ],
            'Manajemen' => [
                1 => [
                    ['code' => 'MN-101', 'name' => 'Pengantar Manajemen', 'sks' => 3],
                    ['code' => 'MN-103', 'name' => 'Pengantar Bisnis', 'sks' => 3],
                    ['code' => 'MN-105', 'name' => 'Matematika Ekonomi', 'sks' => 3],
                    ['code' => 'MN-107', 'name' => 'Akuntansi Pengantar 1', 'sks' => 3],
                    ['code' => 'MN-109', 'name' => 'Bahasa Inggris Bisnis', 'sks' => 2],
                ],
                2 => [
                    ['code' => 'MN-202', 'name' => 'Manajemen Pemasaran', 'sks' => 3],
                    ['code' => 'MN-204', 'name' => 'Manajemen Keuangan', 'sks' => 3],
                    ['code' => 'MN-206', 'name' => 'Manajemen Sumber Daya Manusia', 'sks' => 3],
                    ['code' => 'MN-208', 'name' => 'Akuntansi Pengantar 2', 'sks' => 3],
                    ['code' => 'MN-210', 'name' => 'Statistika Bisnis', 'sks' => 3],
                    ['code' => 'MN-212', 'name' => 'Ekonomi Manajerial', 'sks' => 3],
                ],
                3 => [
                    ['code' => 'MN-301', 'name' => 'Perilaku Organisasi', 'sks' => 3],
                    ['code' => 'MN-303', 'name' => 'Manajemen Operasional', 'sks' => 3],
                    ['code' => 'MN-305', 'name' => 'Etika Bisnis', 'sks' => 3],
                ],
                4 => [
                    ['code' => 'MN-402', 'name' => 'Manajemen Risiko Bisnis', 'sks' => 3],
                    ['code' => 'MN-404', 'name' => 'Sistem Informasi Manajemen', 'sks' => 3],
                    ['code' => 'MN-406', 'name' => 'Riset Pemasaran', 'sks' => 3],
                ],
                5 => [
                    ['code' => 'MN-501', 'name' => 'Manajemen Strategis', 'sks' => 3],
                    ['code' => 'MN-503', 'name' => 'Kewirausahaan', 'sks' => 3],
                    ['code' => 'MN-505', 'name' => 'Studi Kelayakan Bisnis', 'sks' => 3],
                ],
                6 => [
                    ['code' => 'MN-602', 'name' => 'Manajemen Investasi', 'sks' => 3],
                    ['code' => 'MN-604', 'name' => 'Metode Penelitian Manajemen', 'sks' => 2],
                    ['code' => 'MN-606', 'name' => 'Kepemimpinan Bisnis', 'sks' => 3],
                ],
                7 => [
                    ['code' => 'MN-701', 'name' => 'Seminar Manajemen Keuangan/Pemasaran', 'sks' => 3],
                    ['code' => 'MN-703', 'name' => 'Kuliah Kerja Nyata (KKN)', 'sks' => 3],
                    ['code' => 'MN-705', 'name' => 'Magang Manajemen', 'sks' => 4],
                ],
                8 => [
                    ['code' => 'MN-802', 'name' => 'Skripsi / Tugas Akhir Manajemen', 'sks' => 6],
                ]
            ]
        ];

        $courseRows = [];
        $courseByCode = [];
        
        $dayPool = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $roomPool = ['Lab Komputer A', 'Lab Komputer B', 'Lab Jaringan', 'Ruang 401', 'Ruang 402', 'Ruang 403'];
        
        $idx = 0;
        foreach ($catalog as $prodiName => $semesters) {
            $lecturers = $lecturersByProdi[$prodiName] ?? [$admin];
            foreach ($semesters as $semNum => $courses) {
                foreach ($courses as $cIndex => $cData) {
                    $dosen = $lecturers[$idx % count($lecturers)];
                    $idx++;
                    
                    $courseRows[] = [
                        'code' => $cData['code'],
                        'name' => $cData['name'],
                        'sks' => $cData['sks'],
                        'dosen_id' => $dosen->id,
                        'prodi' => $prodiName,
                        'semester' => 'Ganjil 2026/2027',
                        'semester_num' => $semNum,
                        'type' => 'Wajib',
                        'hari' => $dayPool[$idx % count($dayPool)],
                        'jam_mulai' => $idx % 2 === 0 ? '08:00' : '13:00',
                        'jam_selesai' => $idx % 2 === 0 ? '10:30' : '15:30',
                        'ruang' => $roomPool[$idx % count($roomPool)],
                        'attendance_weight' => 10,
                        'assignment_weight' => 20,
                        'uts_weight' => 30,
                        'uas_weight' => 40,
                    ];
                }
            }
        }
        $this->insertRows('courses', $courseRows, $now);
        foreach (DB::table('courses')->get() as $course) {
            $courseByCode[$course->code] = $course;
        }

        $studentGroups = [];
        foreach ($mahasiswaUsers as $student) {
            $studentGroups[$student->prodi][] = $student;
        }

        $coursesByProdi = [];
        foreach ($courseByCode as $course) {
            $coursesByProdi[$course->prodi][] = $course;
        }

        $krsRows = [];
        $approvedEnrollments = [];
        foreach ($mahasiswaUsers as $index => $student) {
            $entryYear = 2025;
            $nim = $student->nim_nip;
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

            $allProdiCourses = $coursesByProdi[$student->prodi] ?? array_values($courseByCode);
            $prodiCourses = array_filter($allProdiCourses, static fn($c) => (int)$c->semester_num === $targetSemester);
            if (empty($prodiCourses)) {
                $prodiCourses = $allProdiCourses;
            }
            $prodiCourses = array_values($prodiCourses);

            $targetSemester = ($entryYear === 2025) ? 2 : 5;
            $allProdiCourses = $coursesByProdi[$student->prodi] ?? [];
            
            $currentSemesterCourses = array_filter($allProdiCourses, static fn($c) => (int)$c->semester_num === $targetSemester);
            if (empty($currentSemesterCourses)) {
                $currentSemesterCourses = $allProdiCourses;
            }
            $courseIds = array_map(static fn ($course) => $course->id, array_values($currentSemesterCourses));

            $krsRows[] = [
                'mahasiswa_id' => $student->id,
                'semester' => 'Ganjil 2026/2027',
                'course_ids' => json_encode($courseIds),
                'status' => 'approved',
                'notes' => 'KRS default mahasiswa aktif yang otomatis disetujui untuk demo.',
            ];

            foreach ($courseIds as $courseId) {
                $approvedEnrollments[$courseId][] = $student->id;
            }

            if ($index % 3 === 0) {
                $pendingSelection = array_slice(array_values($currentSemesterCourses), 0, min(4, count($currentSemesterCourses)));
                $pendingIds = array_map(static fn ($course) => $course->id, $pendingSelection);
                if (count($pendingIds) >= 2) {
                    $krsRows[] = [
                        'mahasiswa_id' => $student->id,
                        'semester' => 'Ganjil 2026/2027',
                        'course_ids' => json_encode($pendingIds),
                        'status' => 'pending',
                        'notes' => 'Menunggu review dosen wali.',
                    ];
                }
            }

            if ($index % 5 === 0) {
                $otherProdiCourseIds = [];
                foreach ($coursesByProdi as $prodiName => $courses) {
                    if ($prodiName !== $student->prodi && !empty($courses)) {
                        $otherProdiCourseIds[] = $courses[0]->id;
                    }
                }
                $rejectedIds = array_values(array_unique(array_merge($courseIds, array_slice($otherProdiCourseIds, 0, 1))));
                $rejectedIds = array_slice($rejectedIds, 0, 3);
                if (count($rejectedIds) >= 2) {
                    $krsRows[] = [
                        'mahasiswa_id' => $student->id,
                        'semester' => 'Ganjil 2026/2027',
                        'course_ids' => json_encode($rejectedIds),
                        'status' => 'rejected',
                        'notes' => 'Ada bentrok jadwal dan kelebihan SKS.',
                    ];
                }
            }
        }

        $this->insertRows('krs_submissions', $krsRows, $now);
        $krsRowsDb = DB::table('krs_submissions')->get()->all();

        $gradeRows = [];
        $submissionRows = [];
        // Seed past semester grades (Semester 1 for targetSemester == 2, Semester 1 & 2 for targetSemester == 5)
        foreach ($mahasiswaUsers as $student) {
            $entryYear = 2025;
            $nim = $student->nim_nip;
            if (strlen($nim) >= 5) {
                $yearPart = substr($nim, 3, 2);
                if (is_numeric($yearPart)) {
                    $entryYear = 2000 + (int)$yearPart;
                }
            }
            $targetSemester = ($entryYear === 2025) ? 2 : 5;
            $allProdiCourses = $coursesByProdi[$student->prodi] ?? [];
            
            $pastSemesters = [];
            for ($s = 1; $s < $targetSemester; $s++) {
                $pastSemesters[] = $s;
            }
            
            foreach ($pastSemesters as $pastSem) {
                $pastCourses = array_filter($allProdiCourses, static fn($c) => (int)$c->semester_num === $pastSem);
                foreach ($pastCourses as $pastC) {
                    $attendanceScore = 80 + (($student->id + $pastC->id) % 5) * 3;
                    $assignmentScore = 78 + (($student->id + $pastC->id) % 6) * 3;
                    $utsScore = 75 + (($student->id + $pastC->id) % 7) * 3;
                    $uasScore = 80 + (($student->id + $pastC->id) % 5) * 4;
                    $finalScore = round(($attendanceScore * 0.1) + ($assignmentScore * 0.2) + ($utsScore * 0.3) + ($uasScore * 0.4), 2);
                    
                    $gradeRows[] = [
                        'mahasiswa_id' => $student->id,
                        'course_id' => $pastC->id,
                        'attendance_score' => $attendanceScore,
                        'assignment_score' => $assignmentScore,
                        'uts_score' => $utsScore,
                        'uas_score' => $uasScore,
                        'score' => $finalScore,
                        'grade' => $this->scoreToLetter($finalScore),
                    ];
                }
            }
        }

        // Seed current semester grades (for approved courses)
        foreach ($approvedEnrollments as $courseId => $studentIds) {
            foreach ($studentIds as $idx => $studentId) {
                $attendanceScore = 78 + (($studentId + $courseId + $idx) % 5) * 3;
                $assignmentScore = 76 + (($studentId + $courseId + $idx) % 6) * 3;
                $utsScore = 74 + (($studentId + $courseId + $idx) % 7) * 3;
                $uasScore = 77 + (($studentId + $courseId + $idx) % 5) * 4;
                $finalScore = round(($attendanceScore * 0.1) + ($assignmentScore * 0.2) + ($utsScore * 0.3) + ($uasScore * 0.4), 2);
                $gradeRows[] = [
                    'mahasiswa_id' => $studentId,
                    'course_id' => $courseId,
                    'attendance_score' => $attendanceScore,
                    'assignment_score' => $assignmentScore,
                    'uts_score' => $utsScore,
                    'uas_score' => $uasScore,
                    'score' => $finalScore,
                    'grade' => $this->scoreToLetter($finalScore),
                ];
            }
        }
        $this->insertRows('grades', $gradeRows, $now);

        $consultationRows = [];
        $studentsWithAdvisor = User::with('dosenWali')->where('role', 'mahasiswa')->get();
        foreach ($studentsWithAdvisor as $student) {
            if (!$student->dosenWali) {
                continue;
            }
            $consultationRows[] = [
                'mahasiswa_id' => $student->id,
                'dosen_id' => $student->dosenWali->id,
                'sender_role' => 'dosen',
                'content' => 'Silakan lanjutkan revisi KRS sesuai catatan yang sudah disetujui.',
                'is_read' => true,
                'read_at' => now()->subDays(3)->toDateTimeString(),
            ];
            $consultationRows[] = [
                'mahasiswa_id' => $student->id,
                'dosen_id' => $student->dosenWali->id,
                'sender_role' => 'mahasiswa',
                'content' => 'Siap Pak/Bu, saya akan ikuti catatan revisinya.',
                'is_read' => true,
                'read_at' => now()->subDays(3)->toDateTimeString(),
            ];
            $consultationRows[] = [
                'mahasiswa_id' => $student->id,
                'dosen_id' => $student->dosenWali->id,
                'sender_role' => 'dosen',
                'content' => 'Pastikan SKS akhir tetap sesuai batas semester ini.',
                'is_read' => true,
                'read_at' => now()->subDays(2)->toDateTimeString(),
            ];
        }
        $this->insertRows('consultation_messages', $consultationRows, $now);

        $submissionTargets = [];
        foreach ($krsRowsDb as $krsRow) {
            if ($krsRow->status !== 'approved') {
                continue;
            }
            $courseIds = json_decode($krsRow->course_ids, true) ?: [];
            if (empty($courseIds)) {
                continue;
            }
            $submissionTargets[] = [
                'mahasiswa_id' => $krsRow->mahasiswa_id,
                'course_id' => $courseIds[0],
                'krs_id' => $krsRow->id,
            ];
        }

        foreach ($submissionTargets as $index => $target) {
            $assignmentRow = DB::table('assignments')->where('course_id', $target['course_id'])->first();
            if (!$assignmentRow) {
                continue;
            }
            $gradeRow = DB::table('grades')
                ->where('mahasiswa_id', $target['mahasiswa_id'])
                ->where('course_id', $target['course_id'])
                ->first();
            $submissionRows[] = [
                'assignment_id' => $assignmentRow->id,
                'mahasiswa_id' => $target['mahasiswa_id'],
                'file_path' => '/storage/submissions/' . $target['mahasiswa_id'] . '-' . $assignmentRow->id . '.pdf',
                'grade' => $gradeRow ? $gradeRow->score : null,
            ];
        }

        $billingRows = [];
        foreach ($mahasiswaUsers as $index => $student) {
            $billingRows[] = [
                'user_id' => $student->id,
                'description' => 'SPP Semester Ganjil 2026/2027',
                'amount' => 3500000,
                'status' => $index % 3 === 0 ? 'Lunas' : 'Belum Lunas',
                'due_date' => now()->addDays(10 + $index)->toDateString(),
            ];
        }
        $this->insertRows('billings', $billingRows, $now);

        $materialRows = [];
        $assignmentRows = [];
        $forumRows = [];
        $quizRows = [];
        $questionRows = [];
        $bapRows = [];
        $attendanceRows = [];
        $attendanceRecordRows = [];
        $edomRows = [];

        $forumReplySeed = [];
        foreach ($courseByCode as $course) {
            $lecturer = User::find($course->dosen_id) ?? $admin;
            $courseStudents = array_values(array_unique($approvedEnrollments[$course->id] ?? []));
            $firstStudentId = $courseStudents[0] ?? ($mahasiswaUsers[0]->id ?? $admin->id);
            $secondStudentId = $courseStudents[1] ?? $firstStudentId;

            $materialRows[] = [
                'course_id' => $course->id,
                'session_num' => 1,
                'type' => 'file',
                'title' => 'RPS dan Kontrak Perkuliahan',
                'content_link' => 'https://umiba.ac.id/materials/' . Str::slug($course->name) . '-rps.pdf',
            ];
            $materialRows[] = [
                'course_id' => $course->id,
                'session_num' => 1,
                'type' => 'meet',
                'title' => 'Link Meet Pertemuan 1',
                'content_link' => 'https://meet.google.com/' . Str::slug($course->code) . '-01',
            ];
            $materialRows[] = [
                'course_id' => $course->id,
                'session_num' => 2,
                'type' => 'file',
                'title' => 'Modul Pertemuan 2',
                'content_link' => 'https://umiba.ac.id/materials/' . Str::slug($course->name) . '-modul-2.pdf',
            ];
            $materialRows[] = [
                'course_id' => $course->id,
                'session_num' => 2,
                'type' => 'meet',
                'title' => 'Link Meet Pertemuan 2',
                'content_link' => 'https://meet.google.com/' . Str::slug($course->code) . '-02',
            ];

            $assignmentRows[] = [
                'course_id' => $course->id,
                'title' => 'Tugas 1 - Studi Kasus ' . $course->name,
                'description' => 'Kerjakan studi kasus sesuai topik yang dibahas di kelas.',
                'deadline' => now()->addDays(14)->toDateString(),
            ];

            $forumRows[] = [
                'course_id' => $course->id,
                'user_id' => $lecturer->id,
                'title' => 'Diskusi Minggu 1: ' . $course->name,
                'content' => 'Silakan diskusikan materi awal dan sampaikan pertanyaan di sini.',
            ];

            $quizRows[] = [
                'course_id' => $course->id,
                'title' => 'Kuis Awal ' . $course->name,
                'duration_minutes' => 30,
                'randomize_questions' => true,
            ];

            $bapRows[] = [
                'course_id' => $course->id,
                'dosen_id' => $lecturer->id,
                'meeting_number' => 1,
                'date' => now()->subDays(7)->toDateString(),
                'topic' => 'Kontrak kuliah dan pengenalan materi ' . $course->name,
                'notes' => 'Perkuliahan berjalan baik dan mahasiswa aktif berdiskusi.',
            ];
            $bapRows[] = [
                'course_id' => $course->id,
                'dosen_id' => $lecturer->id,
                'meeting_number' => 2,
                'date' => now()->subDays(1)->toDateString(),
                'topic' => 'Pembahasan inti dan latihan kasus ' . $course->name,
                'notes' => 'Sebagian mahasiswa sudah submit tugas awal.',
            ];

            $attendanceRows[] = [
                'course_id' => $course->id,
                'meeting_number' => 1,
                'date' => now()->subDays(7)->toDateString(),
            ];
            $attendanceRows[] = [
                'course_id' => $course->id,
                'meeting_number' => 2,
                'date' => now()->subDays(1)->toDateString(),
            ];

            $quizQuestions = [
                [
                    'question' => 'Apa konsep utama pada mata kuliah ' . $course->name . '?',
                    'option_a' => 'Konsep dasar dan penerapan',
                    'option_b' => 'Hanya teori tanpa praktik',
                    'option_c' => 'Diskusi non-akademik',
                    'option_d' => 'Jawaban bebas',
                    'correct_answer' => 'A',
                ],
                [
                    'question' => 'Apa tujuan praktikum pada mata kuliah ini?',
                    'option_a' => 'Menambah beban tugas',
                    'option_b' => 'Menguji pemahaman dan penerapan',
                    'option_c' => 'Mengurangi SKS',
                    'option_d' => 'Tidak ada tujuan khusus',
                    'correct_answer' => 'B',
                ],
                [
                    'question' => 'Dokumen apa yang biasanya dibagikan pada awal semester?',
                    'option_a' => 'RPS dan kontrak kuliah',
                    'option_b' => 'Absensi dosen',
                    'option_c' => 'Nilai akhir',
                    'option_d' => 'Jadwal wisuda',
                    'correct_answer' => 'A',
                ],
            ];

            foreach ($quizQuestions as $q) {
                $questionRows[] = $q + ['__course_code' => $course->code];
            }

            foreach ($courseStudents as $idx => $studentId) {
                $status = match ($idx % 4) {
                    0 => 'present',
                    1 => 'late',
                    2 => 'absent',
                    default => 'present',
                };
                $attendanceRecordRows[] = [
                    'attendance_id' => null,
                    'mahasiswa_id' => $studentId,
                    'status' => $status,
                    '__course_id' => $course->id,
                ];
                $attendanceRecordRows[] = [
                    'attendance_id' => null,
                    'mahasiswa_id' => $studentId,
                    'status' => $idx % 3 === 0 ? 'present' : 'excused',
                    '__course_id' => $course->id,
                ];
            }

            foreach (array_slice($courseStudents, 0, 2) as $idx => $studentId) {
                $edomRows[] = [
                    'dosen_id' => $lecturer->id,
                    'mahasiswa_id' => $studentId,
                    'course_id' => $course->id,
                    'score' => 4 + ($idx % 2),
                    'comment' => $idx === 0 ? 'Dosen menjelaskan materi dengan jelas dan terstruktur.' : 'Interaksi kelas sangat baik dan responsif.',
                ];
            }

            if (!empty($courseStudents)) {
                $forumReplySeed[] = [
                    'course_id' => $course->id,
                    'forum_user_id' => $firstStudentId,
                    'reply_user_id' => $lecturer->id,
                ];
            }
            if (count($courseStudents) > 1) {
                $forumReplySeed[] = [
                    'course_id' => $course->id,
                    'forum_user_id' => $secondStudentId,
                    'reply_user_id' => $firstStudentId,
                ];
            }
        }

        $this->insertRows('materials', $materialRows, $now);
        $this->insertRows('assignments', $assignmentRows, $now);
        $this->insertRows('forums', $forumRows, $now);
        $this->insertRows('quizzes', $quizRows, $now);
        $this->insertRows('baps', $bapRows, $now);
        $this->insertRows('attendances', $attendanceRows, $now);
        $this->insertRows('edoms', $edomRows, $now);
        $this->insertRows('submissions', $submissionRows, $now);

        $quizRowsDb = DB::table('quizzes')->get()->all();
        $questionRowsToInsert = [];
        foreach ($quizRowsDb as $quiz) {
            $courseCode = null;
            foreach ($courseByCode as $code => $course) {
                if ((int) $course->id === (int) $quiz->course_id) {
                    $courseCode = $code;
                    break;
                }
            }
            foreach ($questionRows as $q) {
                if (($q['__course_code'] ?? null) !== $courseCode) {
                    continue;
                }
                $payload = $q;
                unset($payload['__course_code']);
                $payload['quiz_id'] = $quiz->id;
                $questionRowsToInsert[] = $payload;
            }
        }
        $this->insertRows('quiz_questions', $questionRowsToInsert, $now);

        $attendanceRowsDb = DB::table('attendances')->get()->all();
        $attendanceRecordsToInsert = [];
        foreach ($attendanceRowsDb as $attendance) {
            $courseId = $attendance->course_id;
            $enrolledStudents = array_values(array_unique($approvedEnrollments[$courseId] ?? []));
            foreach ($enrolledStudents as $idx => $studentId) {
                $attendanceRecordsToInsert[] = [
                    'attendance_id' => $attendance->id,
                    'mahasiswa_id' => $studentId,
                    'status' => match ($idx % 4) {
                        0 => 'present',
                        1 => 'late',
                        2 => 'absent',
                        default => 'excused',
                    },
                ];
            }
        }
        $this->insertRows('attendance_records', $attendanceRecordsToInsert, $now);

        $forumRowsDb = DB::table('forums')->get()->all();
        $forumRepliesToInsert = [];
        foreach ($forumRowsDb as $forum) {
            $course = null;
            foreach ($courseByCode as $candidate) {
                if ((int) $candidate->id === (int) $forum->course_id) {
                    $course = $candidate;
                    break;
                }
            }
            $lecturer = $course ? User::find($course->dosen_id) : $admin;
            $students = array_values(array_unique($approvedEnrollments[$forum->course_id] ?? []));
            $firstStudent = $students[0] ?? ($mahasiswaUsers[0]->id ?? $admin->id);
            $secondStudent = $students[1] ?? $firstStudent;

            $forumRepliesToInsert[] = [
                'forum_id' => $forum->id,
                'user_id' => $firstStudent,
                'content' => 'Saya setuju, Pak. Bagian ini memang paling penting untuk dipahami.',
            ];
            $forumRepliesToInsert[] = [
                'forum_id' => $forum->id,
                'user_id' => $lecturer?->id ?? $admin->id,
                'content' => 'Betul, silakan baca ulang materi pertemuan pertama sebelum diskusi berikutnya.',
            ];
        }
        $this->insertRows('forum_replies', $forumRepliesToInsert, $now);

        $letterRows = [];
        $letterTypes = ['Surat Keterangan Aktif Kuliah', 'Surat Pengantar Magang', 'Transkrip Sementara'];
        foreach (array_slice($mahasiswaUsers, 0, 3) as $idx => $student) {
            $letterRows[] = [
                'mahasiswa_id' => $student->id,
                'type' => $letterTypes[$idx],
                'date' => now()->subDays(3 - $idx)->toDateString(),
                'status' => match ($idx) {
                    0 => 'Pending',
                    1 => 'Diproses',
                    default => 'Selesai',
                },
                'note' => 'Permohonan administrasi kampus untuk keperluan akademik mahasiswa.',
            ];
        }
        $this->insertRows('letter_requests', $letterRows, $now);

        $calendarRows = [
            ['name' => 'Awal Semester Ganjil 2026/2027', 'start_date' => '2026-08-03', 'end_date' => '2026-08-07', 'type' => 'Akademik'],
            ['name' => 'Pengisian KRS', 'start_date' => '2026-08-10', 'end_date' => '2026-08-14', 'type' => 'Akademik'],
            ['name' => 'Batas Revisi KRS', 'start_date' => '2026-08-17', 'end_date' => '2026-08-19', 'type' => 'Akademik'],
            ['name' => 'UTS Semester Ganjil', 'start_date' => '2026-10-12', 'end_date' => '2026-10-17', 'type' => 'Ujian'],
            ['name' => 'UAS Semester Ganjil', 'start_date' => '2026-12-14', 'end_date' => '2026-12-19', 'type' => 'Ujian'],
            ['name' => 'Rapat Koordinasi Dosen', 'start_date' => '2026-09-05', 'end_date' => '2026-09-05', 'type' => 'Dosen'],
        ];
        $this->insertRows('academic_calendars', $calendarRows, $now);

        $activityRows = [
            ['user_name' => 'Admin UMIBA', 'action' => 'Seed Database', 'details' => 'Mengisi data kampus lengkap dari PDDikti dan data akademik internal.', 'ip_address' => '127.0.0.1'],
            ['user_name' => $dosenUsers[0]->name ?? 'Kaprodi', 'action' => 'Plot Dosen', 'details' => 'Menetapkan dosen pengampu untuk kelas awal semester.', 'ip_address' => '127.0.0.1'],
            ['user_name' => $mahasiswaUsers[0]->name ?? 'Mahasiswa', 'action' => 'Submit KRS', 'details' => 'Mengajukan KRS semester ganjil dengan revisi oleh dosen wali.', 'ip_address' => '127.0.0.1'],
            ['user_name' => $admin->name, 'action' => 'Generate Billing', 'details' => 'Membuat tagihan semester untuk mahasiswa aktif.', 'ip_address' => '127.0.0.1'],
            ['user_name' => $dosenUsers[1]->name ?? 'Dosen', 'action' => 'Create BAP', 'details' => 'Mengisi BAP perkuliahan awal semester.', 'ip_address' => '127.0.0.1'],
            ['user_name' => $admin->name, 'action' => 'Approve KRS', 'details' => 'Menyetujui sebagian pengajuan KRS yang sudah memenuhi syarat.', 'ip_address' => '127.0.0.1'],
        ];
        $this->insertRows('activity_logs', $activityRows, $now);

        $this->command?->info('Seeded campus data: ' . count($dosenUsers) . ' dosen/kaprodi, ' . count($mahasiswaUsers) . ' mahasiswa, ' . count($courseRows) . ' matkul, dan data akademik pendukung.');
    }
}
