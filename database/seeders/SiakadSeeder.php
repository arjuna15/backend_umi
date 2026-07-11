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
            'Magister Manajemen' => ['code' => 'MM', 'jenjang' => 'S2'],
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
                    ['code' => 'MIP70112', 'name' => 'Pengantar Ilmu Komputer', 'sks' => 2],
                    ['code' => 'MIP70213', 'name' => 'Sistem Basis Data', 'sks' => 3],
                    ['code' => 'MIP70313', 'name' => 'Kalkulus', 'sks' => 3],
                    ['code' => 'MIP70413', 'name' => 'Metode Statistika', 'sks' => 3],
                    ['code' => 'MWF0113', 'name' => 'Algoritma dan Pemrograman', 'sks' => 3],
                    ['code' => 'MWF0212', 'name' => 'Bahasa Inggris 1', 'sks' => 2],
                    ['code' => 'MWP0112', 'name' => 'Pancasila', 'sks' => 2],
                    ['code' => 'MWP0212', 'name' => 'Agama', 'sks' => 2],
                ],
                2 => [
                    ['code' => 'MIP70523', 'name' => 'Arsitektur dan Organisasi Komputer', 'sks' => 3],
                    ['code' => 'MIP70623', 'name' => 'Rekayasa Perangkat Lunak', 'sks' => 3],
                    ['code' => 'MIP70723', 'name' => 'Matematika Diskrit', 'sks' => 3],
                    ['code' => 'MIP70823', 'name' => 'Aljabar Linear Elementer', 'sks' => 3],
                    ['code' => 'MWF0322', 'name' => 'Bahasa Inggris 2', 'sks' => 2],
                    ['code' => 'MWP0322', 'name' => 'Kewarganegaraan', 'sks' => 2],
                    ['code' => 'MWP0422', 'name' => 'Bahasa Indonesia', 'sks' => 2],
                    ['code' => 'MWU0122', 'name' => 'Service Excellence', 'sks' => 2],
                ],
                3 => [
                    ['code' => 'MIP70933', 'name' => 'Sistem Digital', 'sks' => 3],
                    ['code' => 'MIP71033', 'name' => 'Komunikasi Data dan Jaringan', 'sks' => 3],
                    ['code' => 'MIP71133', 'name' => 'Teknik Kompilasi', 'sks' => 3],
                    ['code' => 'MIP71232', 'name' => 'Sistem Operasi', 'sks' => 2],
                    ['code' => 'MIP71333', 'name' => 'Administrasi Basis Data', 'sks' => 3],
                    ['code' => 'MIP71433', 'name' => 'Pengembangan Aplikasi Sederhana', 'sks' => 3],
                    ['code' => 'MIP71533', 'name' => 'Tata Kelola Teknologi Informasi', 'sks' => 2],
                    ['code' => 'MWU0232', 'name' => 'Kewirausahaan', 'sks' => 2],
                ],
                4 => [
                    ['code' => 'MIP71643', 'name' => 'Metode Numerik', 'sks' => 3],
                    ['code' => 'MIP71743', 'name' => 'Pengembangan Aplikasi Berorientasi Objek', 'sks' => 3],
                    ['code' => 'MIP71843', 'name' => 'Pemrograman Basis Data SQL & PL/SQL', 'sks' => 3],
                    ['code' => 'MIP71943', 'name' => 'Sistem Keamanan Jaringan', 'sks' => 3],
                    ['code' => 'MWU0342', 'name' => 'Kecerdasan Emosional', 'sks' => 2],
                    ['code' => 'MWU0442', 'name' => 'Leadership', 'sks' => 2],
                    ['code' => 'MWU0542', 'name' => 'Public Speaking', 'sks' => 2],
                    ['code' => 'MWU0642', 'name' => 'Kreativitas dan Inovasi', 'sks' => 2],
                ],
                5 => [
                    ['code' => 'MIP72053', 'name' => 'Penerapan ERP', 'sks' => 3],
                    ['code' => 'MIP72152', 'name' => 'Pengujian Perangkat Lunak', 'sks' => 2],
                    ['code' => 'MIP72253', 'name' => 'Deep Learning', 'sks' => 3],
                    ['code' => 'MIP72352', 'name' => 'Komputasi Awan', 'sks' => 2],
                    ['code' => 'MIP72453', 'name' => 'Kriptografi', 'sks' => 3],
                    ['code' => 'MIP72553', 'name' => 'Analisis Big Data dan Data Sains', 'sks' => 3],
                    ['code' => 'MWU0752', 'name' => 'Metodologi Penelitian', 'sks' => 2],
                    ['code' => 'MWU0852', 'name' => 'Business Network', 'sks' => 2],
                ],
                6 => [
                    ['code' => 'MIP72663', 'name' => 'Requirement Engineering', 'sks' => 3],
                    ['code' => 'MIP72762', 'name' => 'Teknik Simulasi & Data Mining', 'sks' => 2],
                    ['code' => 'MIP72862', 'name' => 'Simulasi Bisnis', 'sks' => 2],
                    ['code' => 'MIP72963', 'name' => 'Sistem Pendukung Keputusan', 'sks' => 3],
                    ['code' => 'MIP73062', 'name' => 'Penjaminan Mutu Perangkat Lunak', 'sks' => 2],
                    ['code' => 'MWU0962', 'name' => 'Pemasaran dan Promosi', 'sks' => 2],
                    ['code' => 'MWU1062', 'name' => 'Komunikasi dan Negosiasi', 'sks' => 2],
                    ['code' => 'MWU1162', 'name' => 'KKN Tematik', 'sks' => 3],
                ],
                7 => [
                    ['code' => 'MIP73173', 'name' => 'Pengembangan Teknologi Kreatif', 'sks' => 3],
                    ['code' => 'MIP73273', 'name' => 'Data & Text Mining', 'sks' => 3],
                    ['code' => 'MPP71172', 'name' => 'Pengembangan Aplikasi Berbasis Web', 'sks' => 2],
                    ['code' => 'MPP71272', 'name' => 'Pengembangan Aplikasi Berbasis Sistem', 'sks' => 2],
                    ['code' => 'MPP71372', 'name' => 'Pengembangan Aplikasi Berbasis Mobile', 'sks' => 2],
                    ['code' => 'MWU1273', 'name' => 'Kecerdasan Buatan', 'sks' => 3],
                    ['code' => 'MWU1373', 'name' => 'Magang Profesi', 'sks' => 3],
                ],
                8 => [
                    ['code' => 'MIP73386', 'name' => 'Tugas Akhir', 'sks' => 6],
                ]
            ],
            'Sistem Dan Teknologi Informasi' => [
                1 => [
                    ['code' => 'MIP80112', 'name' => 'Konsep Sistem Informasi', 'sks' => 2],
                    ['code' => 'MIP80213', 'name' => 'Sistem Basis Data', 'sks' => 3],
                    ['code' => 'MIP80313', 'name' => 'Kalkulus', 'sks' => 3],
                    ['code' => 'MIP80413', 'name' => 'Metode Statistika', 'sks' => 3],
                    ['code' => 'MWF0113', 'name' => 'Algoritma dan Pemrograman', 'sks' => 3],
                    ['code' => 'MWF0212', 'name' => 'Bahasa Inggris 1', 'sks' => 2],
                    ['code' => 'MWP0112', 'name' => 'Pancasila', 'sks' => 2],
                    ['code' => 'MWP0212', 'name' => 'Agama', 'sks' => 2],
                ],
                2 => [
                    ['code' => 'MIP80523', 'name' => 'Arsitektur Komputer', 'sks' => 3],
                    ['code' => 'MIP80623', 'name' => 'Rekayasa Perangkat Lunak', 'sks' => 3],
                    ['code' => 'MIP80723', 'name' => 'Matematika Diskrit', 'sks' => 3],
                    ['code' => 'MIP80823', 'name' => 'Aljabar Linear Elementer', 'sks' => 3],
                    ['code' => 'MWF0322', 'name' => 'Bahasa Inggris 2', 'sks' => 2],
                    ['code' => 'MWP0322', 'name' => 'Kewarganegaraan', 'sks' => 2],
                    ['code' => 'MWP0422', 'name' => 'Bahasa Indonesia', 'sks' => 2],
                    ['code' => 'MWU0122', 'name' => 'Service Excellence', 'sks' => 2],
                ],
                3 => [
                    ['code' => 'MIP80933', 'name' => 'Pengujian dan Implementasi Sistem', 'sks' => 3],
                    ['code' => 'MIP81033', 'name' => 'Riset dan Desain Pengalaman Pengguna', 'sks' => 3],
                    ['code' => 'MIP81133', 'name' => 'Komputasi Kolaboratif', 'sks' => 3],
                    ['code' => 'MIP81232', 'name' => 'Sistem Operasi', 'sks' => 2],
                    ['code' => 'MIP81332', 'name' => 'Administrasi Basis Data', 'sks' => 2],
                    ['code' => 'MIP81433', 'name' => 'Design e-Business', 'sks' => 3],
                    ['code' => 'MIP81533', 'name' => 'Tata Kelola Teknologi Informasi', 'sks' => 3],
                    ['code' => 'MWU0232', 'name' => 'Kewirausahaan', 'sks' => 2],
                ],
                4 => [
                    ['code' => 'MIP81643', 'name' => 'Metode Numerik', 'sks' => 3],
                    ['code' => 'MIP81743', 'name' => 'Pengembangan Aplikasi Berorientasi Objek', 'sks' => 3],
                    ['code' => 'MIP81843', 'name' => 'Pemrograman Basis Data SQL & PL/SQL', 'sks' => 3],
                    ['code' => 'MIP81943', 'name' => 'Keamanan Sistem Jaringan', 'sks' => 3],
                    ['code' => 'MWU0342', 'name' => 'Kecerdasan Emosional', 'sks' => 2],
                    ['code' => 'MWU0442', 'name' => 'Leadership', 'sks' => 2],
                    ['code' => 'MWU0542', 'name' => 'Public Speaking', 'sks' => 2],
                    ['code' => 'MWU0642', 'name' => 'Kreativitas dan Inovasi', 'sks' => 2],
                ],
                5 => [
                    ['code' => 'MIP82053', 'name' => 'Penerapan ERP', 'sks' => 3],
                    ['code' => 'MIP82153', 'name' => 'Digital & Media Baru', 'sks' => 3],
                    ['code' => 'MIP82253', 'name' => 'Teknik Visualisasi Grafis', 'sks' => 3],
                    ['code' => 'MIP82353', 'name' => 'Komputasi Awan', 'sks' => 3],
                    ['code' => 'MIP82452', 'name' => 'Analisa Sistem Informasi Desain', 'sks' => 2],
                    ['code' => 'MIP82553', 'name' => 'Kriptografi', 'sks' => 3],
                    ['code' => 'MWU0752', 'name' => 'Metodologi Penelitian', 'sks' => 2],
                    ['code' => 'MWU0852', 'name' => 'Business Network', 'sks' => 2],
                ],
                6 => [
                    ['code' => 'MIP82563', 'name' => 'Pengolahan Citra', 'sks' => 3],
                    ['code' => 'MIP82662', 'name' => 'Teknik Simulasi & Data Mining', 'sks' => 2],
                    ['code' => 'MIP82762', 'name' => 'Simulasi Bisnis', 'sks' => 2],
                    ['code' => 'MIP82863', 'name' => 'Sistem Pendukung Keputusan', 'sks' => 3],
                    ['code' => 'MIP82962', 'name' => 'Penjaminan Mutu Perangkat Lunak', 'sks' => 2],
                    ['code' => 'MWU0962', 'name' => 'Pemasaran dan Promosi', 'sks' => 2],
                    ['code' => 'MWU1062', 'name' => 'Komunikasi dan Negosiasi', 'sks' => 2],
                    ['code' => 'MWU1162', 'name' => 'KKN Tematik', 'sks' => 2],
                ],
                7 => [
                    ['code' => 'MIP83073', 'name' => 'Pengembangan Aplikasi Bisnis', 'sks' => 3],
                    ['code' => 'MIP83173', 'name' => 'Data & Text Mining', 'sks' => 3],
                    ['code' => 'MPP81172', 'name' => 'UI/UX Designer', 'sks' => 2],
                    ['code' => 'MPP81272', 'name' => 'Art Design Culture', 'sks' => 2],
                    ['code' => 'MPP81372', 'name' => 'Motion Graphics', 'sks' => 2],
                    ['code' => 'MWU1273', 'name' => 'Kecerdasan Buatan', 'sks' => 3],
                    ['code' => 'MWU1373', 'name' => 'Magang Profesi', 'sks' => 3],
                ],
                8 => [
                    ['code' => 'MIP83386', 'name' => 'Tugas Akhir', 'sks' => 6],
                ]
            ],
            'Ilmu Aktuaria' => [
                1 => [
                    ['code' => 'MIP60113', 'name' => 'Kalkulus 1', 'sks' => 3],
                    ['code' => 'MIP60213', 'name' => 'Metode Statistika', 'sks' => 3],
                    ['code' => 'MIP60312', 'name' => 'Pengantar Teori Peluang', 'sks' => 2],
                    ['code' => 'MIP60413', 'name' => 'Pengantar Ilmu Ekonomi', 'sks' => 3],
                    ['code' => 'MWF0113', 'name' => 'Algoritma dan Pemrograman', 'sks' => 3],
                    ['code' => 'MWF0212', 'name' => 'Bahasa Inggris 1', 'sks' => 2],
                    ['code' => 'MWP0112', 'name' => 'Pancasila', 'sks' => 2],
                    ['code' => 'MWP0212', 'name' => 'Agama', 'sks' => 2],
                ],
                2 => [
                    ['code' => 'MIP60523', 'name' => 'Kalkulus 2', 'sks' => 3],
                    ['code' => 'MIP60623', 'name' => 'Aljabar Linear Elementer', 'sks' => 3],
                    ['code' => 'MIP60723', 'name' => 'Probabilitas dan Statistika 1', 'sks' => 3],
                    ['code' => 'MIP60823', 'name' => 'Ekonomi Mikro Dan Makro', 'sks' => 3],
                    ['code' => 'MWF0322', 'name' => 'Bahasa Inggris 2', 'sks' => 2],
                    ['code' => 'MWP0322', 'name' => 'Kewarganegaraan', 'sks' => 2],
                    ['code' => 'MWP0422', 'name' => 'Bahasa Indonesia', 'sks' => 2],
                    ['code' => 'MWU0122', 'name' => 'Service Excellence', 'sks' => 2],
                ],
                3 => [
                    ['code' => 'MIP60933', 'name' => 'Ekonomi Islam', 'sks' => 3],
                    ['code' => 'MIP61033', 'name' => 'Matematika Finansial 1', 'sks' => 3],
                    ['code' => 'MIP61133', 'name' => 'Probabilitas dan Statistika 2', 'sks' => 3],
                    ['code' => 'MIP61233', 'name' => 'Persamaan Differensial', 'sks' => 3],
                    ['code' => 'MIP61333', 'name' => 'Pengantar Aktuaria dan Asuransi', 'sks' => 3],
                    ['code' => 'MIP61433', 'name' => 'Akuntansi Aktuaria 1', 'sks' => 3],
                    ['code' => 'MWU0232', 'name' => 'Kewirausahaan', 'sks' => 2],
                ],
                4 => [
                    ['code' => 'MIP61543', 'name' => 'Matematika Finansial 2', 'sks' => 3],
                    ['code' => 'MIP61643', 'name' => 'Proses Stokastik Dasar', 'sks' => 3],
                    ['code' => 'MIP61743', 'name' => 'Metode Numerik', 'sks' => 3],
                    ['code' => 'MIP61843', 'name' => 'Akuntansi Aktuaria 2', 'sks' => 3],
                    ['code' => 'MWU0342', 'name' => 'Kecerdasan Emosional', 'sks' => 2],
                    ['code' => 'MWU0442', 'name' => 'Leadership', 'sks' => 2],
                    ['code' => 'MWU0542', 'name' => 'Public Speaking', 'sks' => 2],
                    ['code' => 'MWU0642', 'name' => 'Kreativitas dan Inovasi', 'sks' => 2],
                ],
                5 => [
                    ['code' => 'MIP61953', 'name' => 'Asuransi Syariah', 'sks' => 3],
                    ['code' => 'MIP62053', 'name' => 'Matematika Aktuaria 1', 'sks' => 3],
                    ['code' => 'MIP62153', 'name' => 'Teori Risiko 1', 'sks' => 3],
                    ['code' => 'MIP62253', 'name' => 'Metode Statistika Aktuaria 1', 'sks' => 3],
                    ['code' => 'MIP62353', 'name' => 'Manajemen Risiko', 'sks' => 3],
                    ['code' => 'MWU0752', 'name' => 'Metodologi Penelitian', 'sks' => 2],
                    ['code' => 'MWU0852', 'name' => 'Business Network', 'sks' => 2],
                ],
                6 => [
                    ['code' => 'MIP62463', 'name' => 'Asuransi Siber', 'sks' => 3],
                    ['code' => 'MIP62563', 'name' => 'Matematika Aktuaria 2', 'sks' => 3],
                    ['code' => 'MIP62663', 'name' => 'Teori Risiko 2', 'sks' => 3],
                    ['code' => 'MIP62763', 'name' => 'Metode Statistika Aktuaria 2', 'sks' => 3],
                    ['code' => 'MWU0962', 'name' => 'Pemasaran dan Promosi', 'sks' => 2],
                    ['code' => 'MWU1062', 'name' => 'Komunikasi dan Negosiasi', 'sks' => 2],
                    ['code' => 'MWU1162', 'name' => 'KKN Tematik', 'sks' => 3],
                ],
                7 => [
                    ['code' => 'MIP62873', 'name' => 'Teori Pendanaan Pensiun', 'sks' => 3],
                    ['code' => 'MIP62973', 'name' => 'Pemodelan Keuangan Derivatif', 'sks' => 3],
                    ['code' => 'MIP63072', 'name' => 'Simulasi Model Aktuaria', 'sks' => 2],
                    ['code' => 'MIP63173', 'name' => 'Analisa dan Sains Data (Artificial Intel)', 'sks' => 3],
                    ['code' => 'MPP70173', 'name' => 'Ekonometrika', 'sks' => 3],
                    ['code' => 'MWU1273', 'name' => 'Kecerdasan Buatan', 'sks' => 3],
                    ['code' => 'MWU1373', 'name' => 'Magang Profesi', 'sks' => 3],
                ],
                8 => [
                    ['code' => 'MIP63386', 'name' => 'Tugas Akhir', 'sks' => 6],
                ]
            ],
            'Hukum' => [
                1 => [
                    ['code' => 'HKMWU51201', 'name' => 'Pendidikan Agama', 'sks' => 2],
                    ['code' => 'HKM51401', 'name' => 'Pengantar Ilmu Hukum', 'sks' => 4],
                    ['code' => 'HKM51402', 'name' => 'Pengantar Hukum Indonesia', 'sks' => 4],
                    ['code' => 'HKM51203', 'name' => 'Ilmu Negara', 'sks' => 2],
                    ['code' => 'HKM51204', 'name' => 'Bahasa Inggris (Hukum)', 'sks' => 2],
                    ['code' => 'HKMWU51202', 'name' => 'Pendidikan Pancasila', 'sks' => 2],
                    ['code' => 'HKMWU51203', 'name' => 'Bahasa Indonesia', 'sks' => 2],
                    ['code' => 'HKMWU51204', 'name' => 'Kewarganegaraan', 'sks' => 2],
                ],
                2 => [
                    ['code' => 'HKM52206', 'name' => 'Hukum Adat', 'sks' => 2],
                    ['code' => 'HKM52307', 'name' => 'Hukum Administrasi Negara', 'sks' => 3],
                    ['code' => 'HKM52208', 'name' => 'Hukum Islam', 'sks' => 2],
                    ['code' => 'HKM52409', 'name' => 'Hukum Pidana', 'sks' => 4],
                    ['code' => 'HKM52410', 'name' => 'Hukum Perdata', 'sks' => 4],
                    ['code' => 'HKM52311', 'name' => 'Hukum Tata Negara', 'sks' => 3],
                    ['code' => 'HKM52212', 'name' => 'Hukum Internasional', 'sks' => 2],
                ],
                3 => [
                    ['code' => 'HKM53213', 'name' => 'Hukum Keluarga', 'sks' => 2],
                    ['code' => 'HKM53414', 'name' => 'Hukum Perikatan', 'sks' => 4],
                    ['code' => 'HKM53215', 'name' => 'Hukum Benda', 'sks' => 2],
                    ['code' => 'HKM53216', 'name' => 'Hukum Pajak', 'sks' => 2],
                    ['code' => 'HKM53217', 'name' => 'Hukum Pemerintahan Daerah', 'sks' => 2],
                    ['code' => 'HKM53318', 'name' => 'Hukum Lingkungan dan Tata Ruang', 'sks' => 3],
                    ['code' => 'HKM53319', 'name' => 'Hukum Agraria', 'sks' => 3],
                    ['code' => 'HKM53220', 'name' => 'Logika Hukum', 'sks' => 2],
                ],
                4 => [
                    ['code' => 'HKM54321', 'name' => 'Hukum Acara Perdata', 'sks' => 3],
                    ['code' => 'HKM54322', 'name' => 'Hukum Acara Pidana', 'sks' => 3],
                    ['code' => 'HKM54223', 'name' => 'Ilmu Perundang-Undangan', 'sks' => 2],
                    ['code' => 'HKM54224', 'name' => 'Hukum Ketenagakerjaan', 'sks' => 2],
                    ['code' => 'HKM54225', 'name' => 'Hukum Ekonomi', 'sks' => 2],
                    ['code' => 'HKM54226', 'name' => 'Hukum Lembaga-Lembaga Negara', 'sks' => 2],
                    ['code' => 'HKM54227', 'name' => 'Hukum Perjanjian Internasional', 'sks' => 2],
                    ['code' => 'HKM54228', 'name' => 'Hukum Dagang', 'sks' => 2],
                    ['code' => 'HKM54229', 'name' => 'Hukum Waris Perdata', 'sks' => 2],
                ],
                5 => [
                    ['code' => 'HKM55330', 'name' => 'Hukum Acara Administrasi Negara', 'sks' => 3],
                    ['code' => 'HKM55231', 'name' => 'Hukum Perbankan', 'sks' => 2],
                    ['code' => 'HKM55232', 'name' => 'Hukum Pidana Ekonomi', 'sks' => 2],
                    ['code' => 'HKM55233', 'name' => 'Teknik Perancangan Peraturan Perundang-undangan', 'sks' => 2],
                    ['code' => 'HKM55334', 'name' => 'Hukum Perdata Internasional', 'sks' => 3],
                    ['code' => 'HKM55235', 'name' => 'Hukum Kekayaan Intelektual', 'sks' => 2],
                    ['code' => 'HKM55236', 'name' => 'Hukum Hak Asasi Manusia', 'sks' => 2],
                    ['code' => 'HKM55237', 'name' => 'Hukum Perusahaan', 'sks' => 2],
                    ['code' => 'HKM55238', 'name' => 'Cyber Law/Hukum Teknologi, Informasi dan Komunikasi', 'sks' => 2],
                ],
                6 => [
                    ['code' => 'HKM56239', 'name' => 'Alternatif Penyelesaian Sengketa', 'sks' => 2],
                    ['code' => 'HKM56240', 'name' => 'Hukum Kontrak Internasional', 'sks' => 2],
                    ['code' => 'HKM56241', 'name' => 'Hukum Organisasi Internasional', 'sks' => 2],
                    ['code' => 'HKM56242', 'name' => 'Perancangan Kontrak Bisnis', 'sks' => 2],
                    ['code' => 'HKM56343', 'name' => 'Metode Penelitian dan Penulisan Hukum', 'sks' => 3],
                    ['code' => 'HKM56244', 'name' => 'Sosiologi Hukum', 'sks' => 2],
                    ['code' => 'HKM56345', 'name' => 'Penemuan dan Penafsiran Hukum', 'sks' => 3],
                    ['code' => 'HKM56246', 'name' => 'Hukum dan Masyarakat', 'sks' => 2],
                    ['code' => 'HKM56247', 'name' => 'Filsafat Hukum', 'sks' => 2],
                ],
                7 => [
                    ['code' => 'HKM57248', 'name' => 'Kriminologi', 'sks' => 2],
                    ['code' => 'HKM57249', 'name' => 'Etika Profesi', 'sks' => 2],
                    ['code' => 'HKM57250', 'name' => 'Perbandingan Hukum', 'sks' => 2],
                    ['code' => 'HKM57252', 'name' => 'Seminar Penulisan Hukum', 'sks' => 2],
                    ['code' => 'HKMP57253', 'name' => 'Mata Kuliah Pilihan (Konsentrasi)', 'sks' => 6],
                ],
                8 => [
                    ['code' => 'HKM58454', 'name' => 'Tugas Akhir (Skripsi)', 'sks' => 4],
                    ['code' => 'HKMP58655', 'name' => 'Mata Kuliah Pilihan', 'sks' => 6],
                ]
            ],
            'Manajemen' => [
                1 => [
                    ['code' => 'MKK11', 'name' => 'Pengantar Bisnis', 'sks' => 3],
                    ['code' => 'MKK12', 'name' => 'Pengantar Manajemen', 'sks' => 3],
                    ['code' => 'MKK13', 'name' => 'Pengantar Akuntansi', 'sks' => 3],
                    ['code' => 'MKK14', 'name' => 'Matematika Ekonomi', 'sks' => 3],
                    ['code' => 'MKK15', 'name' => 'Pengantar Ilmu Ekonomi', 'sks' => 3],
                    ['code' => 'MPK11', 'name' => 'Pendidikan Agama', 'sks' => 2],
                    ['code' => 'MPK12', 'name' => 'Pendidikan Pancasila', 'sks' => 2],
                    ['code' => 'MPK24', 'name' => 'Bahasa Inggris', 'sks' => 2],
                ],
                2 => [
                    ['code' => 'MKK21', 'name' => 'Statistik Ekonomi', 'sks' => 3],
                    ['code' => 'MKK72', 'name' => 'Aspek Hukum dalam Bisnis', 'sks' => 3],
                    ['code' => 'MKK23', 'name' => 'Ekonomi Makro & Mikro', 'sks' => 3],
                    ['code' => 'MPB21', 'name' => 'Teknologi Sosial Media', 'sks' => 2],
                    ['code' => 'MPB22', 'name' => 'Service Excellence / Pelayanan Prima', 'sks' => 3],
                    ['code' => 'MPK21', 'name' => 'Pendidikan Bahasa Indonesia', 'sks' => 2],
                    ['code' => 'MPK22', 'name' => 'Pendidikan Kewarganegaraan', 'sks' => 2],
                    ['code' => 'MPK25', 'name' => 'Bahasa Inggris Bisnis', 'sks' => 2],
                ],
                3 => [
                    ['code' => 'MKB31', 'name' => 'Akuntansi Manajemen', 'sks' => 3],
                    ['code' => 'MKB32', 'name' => 'Manajemen Keuangan', 'sks' => 3],
                    ['code' => 'MKB83', 'name' => 'Manajemen Produksi dan Operasi', 'sks' => 3],
                    ['code' => 'MKB34', 'name' => 'Manajemen Pemasaran', 'sks' => 3],
                    ['code' => 'MKB35', 'name' => 'Manajemen Sumber Daya Manusia', 'sks' => 3],
                    ['code' => 'MKK31', 'name' => 'Kewirausahaan & UKM', 'sks' => 3],
                    ['code' => 'MKK73', 'name' => 'E-Commerce', 'sks' => 2],
                    ['code' => 'MKB33', 'name' => 'Manajemen Talenta', 'sks' => 2],
                ],
                4 => [
                    ['code' => 'MKB82', 'name' => 'Perilaku Organisasi', 'sks' => 3],
                    ['code' => 'MKB42', 'name' => 'Manajemen Kreatif & Inovasi', 'sks' => 3],
                    ['code' => 'MKB43', 'name' => 'Money Market & Capital Market', 'sks' => 3],
                    ['code' => 'MPB85', 'name' => 'Manajemen Risiko', 'sks' => 3],
                    ['code' => 'MPB42', 'name' => 'Kepemimpinan', 'sks' => 3],
                    ['code' => 'MPB43', 'name' => 'Public Speaking', 'sks' => 3],
                    ['code' => 'MPB44', 'name' => 'Kecerdasan Emosional', 'sks' => 2],
                    ['code' => 'MPB64', 'name' => 'Star Up', 'sks' => 2],
                ],
                5 => [
                    ['code' => 'MKBA1', 'name' => 'Strategi Pemasaran', 'sks' => 3],
                    ['code' => 'MKBA2', 'name' => 'Manajemen Ritel', 'sks' => 3],
                    ['code' => 'MBB51', 'name' => 'Perekonomian Indonesia', 'sks' => 3],
                    ['code' => 'MKB51', 'name' => 'Studi Kelayakan Bisnis', 'sks' => 3],
                    ['code' => 'MKB84', 'name' => 'Manajemen Internasional', 'sks' => 3],
                    ['code' => 'MKK51', 'name' => 'Metodologi Penelitian', 'sks' => 2],
                    ['code' => 'MKK52', 'name' => 'Perpajakan', 'sks' => 3],
                ],
                6 => [
                    ['code' => 'MKBA3', 'name' => 'Pemasaran Jasa', 'sks' => 3],
                    ['code' => 'MKK61', 'name' => 'Seminar Pemasaran', 'sks' => 3],
                    ['code' => 'MKK74', 'name' => 'Teknologi Pengolahan Data', 'sks' => 2],
                    ['code' => 'MKK62', 'name' => 'Change Management', 'sks' => 3],
                    ['code' => 'MPB61', 'name' => 'Teori Pengambilan Keputusan', 'sks' => 3],
                    ['code' => 'MPB62', 'name' => 'Komunikasi & Negosiasi Bisnis', 'sks' => 3],
                    ['code' => 'MPB63', 'name' => 'Sistem Informasi Manajemen', 'sks' => 3],
                ],
                7 => [
                    ['code' => 'MBB71', 'name' => 'Seminar Proposal', 'sks' => 2],
                    ['code' => 'MBB72', 'name' => 'Manajemen Logistik', 'sks' => 3],
                    ['code' => 'MBB73', 'name' => 'KKN / Magang', 'sks' => 2],
                    ['code' => 'MBB74', 'name' => 'Perilaku Konsumen', 'sks' => 3],
                ],
                8 => [
                    ['code' => 'MBB81', 'name' => 'Skripsi', 'sks' => 6],
                    ['code' => 'MKB81', 'name' => 'Manajemen Strategik', 'sks' => 3],
                ]
            ],
            'Magister Manajemen' => [
                1 => [
                    ['code' => 'MM1301', 'name' => 'Manajemen Talenta & Pengembangan SDM Digital', 'sks' => 3],
                    ['code' => 'MM1302', 'name' => 'Pemasaran Digital & Perilaku Pelanggan', 'sks' => 3],
                    ['code' => 'MM1303', 'name' => 'Keuangan Digital & Teknologi Sistem Pembayaran', 'sks' => 3],
                    ['code' => 'MM1304', 'name' => 'Manajemen Operasi & Supply Chain Digital', 'sks' => 3],
                    ['code' => 'MM1305', 'name' => 'Manajemen Strategik', 'sks' => 3],
                ],
                2 => [
                    ['code' => 'MM1301', 'name' => 'Metodologi Penelitian & Analisis Data Bisnis Digital', 'sks' => 3],
                    ['code' => 'MM1302', 'name' => 'Kepemimpinan Transformasional & Transformasi Digital', 'sks' => 3],
                    ['code' => 'MM1303', 'name' => 'Business Analytics & Digital Decision Making', 'sks' => 3],
                    ['code' => 'MM23HR1', 'name' => 'Seminar Manajemen Talenta & Kompetensi', 'sks' => 3],
                    ['code' => 'MM23HR2', 'name' => 'Seminar Human Capital Analytics digital & Kinerja SDM', 'sks' => 3],
                    ['code' => 'MM22SP1', 'name' => 'Seminar Proposal Penelitian', 'sks' => 2],
                ],
                3 => [
                    ['code' => 'MM32PUB1', 'name' => 'Publikasi Artikel Ilmiah (LoA/Terbit)', 'sks' => 2],
                    ['code' => 'MM34TA1', 'name' => 'Tesis', 'sks' => 4],
                ],
                4 => [],
                5 => [],
                6 => [],
                7 => [],
                8 => []
            ],
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
                        'code' => $cData['code'] . '-' . ($prodiDefinitions[$prodiName]['code'] ?? 'GEN') . '-S' . $semNum,
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
