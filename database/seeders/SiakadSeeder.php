<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SiakadSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        foreach ([
            'users',
            'courses',
            'grades',
            'billings',
            'materials',
            'assignments',
            'attendances',
            'attendance_records',
            'submissions',
            'forums',
            'forum_replies',
            'krs_submissions',
            'edoms',
            'quizzes',
            'quiz_questions',
            'baps',
            'academic_calendars',
        ] as $table) {
            DB::table($table)->truncate();
        }
        Schema::enableForeignKeyConstraints();

        $seedBase = database_path('seed-data/pddikti');
        $dosenFile = $seedBase . '/dosen.json';
        $mahasiswaFile = $seedBase . '/mahasiswa.json';

        if (!file_exists($dosenFile) || !file_exists($mahasiswaFile)) {
            $this->command?->warn('PDDikti seed files not found. Skipping SIAKAD user seeding.');
            return;
        }

        $dosenData = json_decode(file_get_contents($dosenFile), true) ?: [];
        $mahasiswaData = json_decode(file_get_contents($mahasiswaFile), true) ?: [];

        $normalize = static function (?string $value): string {
            return Str::of((string) $value)->trim()->squish()->title()->toString();
        };

        $slugifyEmail = static function (string $name, string $suffix): string {
            $base = Str::slug($name);
            if ($base === '') {
                $base = 'user';
            }
            return $base . $suffix;
        };

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
        foreach ($dosenData as $row) {
            $prodi = $normalize($row['nama_prodi'] ?? '-');
            if ($prodi !== '-' && !isset($prodiLeadLecturer[$prodi])) {
                $prodiLeadLecturer[$prodi] = $row;
            }
        }

        $dosenUsers = [];
        foreach ($dosenData as $index => $row) {
            $name = $normalize($row['nama'] ?? 'Dosen ' . ($index + 1));
            $prodi = $normalize($row['nama_prodi'] ?? '-');
            $baseId = trim((string) ($row['nidn'] ?? '')) !== '' ? trim((string) $row['nidn']) : trim((string) ($row['nuptk'] ?? ''));
            $nimNip = $baseId !== '' ? $baseId : 'DOSEN' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);
            $isKaprodi = isset($prodiLeadLecturer[$prodi]) && ($prodiLeadLecturer[$prodi]['id'] ?? null) === ($row['id'] ?? null);
            $role = $isKaprodi ? 'kaprodi' : 'dosen';

            $dosenUsers[] = User::create([
                'name' => $name,
                'email' => $slugifyEmail($name, '.' . strtolower($role) . '.' . $nimNip . '@umiba.ac.id'),
                'password' => Hash::make('password123'),
                'role' => $role,
                'nim_nip' => $nimNip,
                'prodi' => $prodi,
                'status' => 'Aktif',
                'jfa' => $role === 'kaprodi' ? 'Kaprodi' : 'Dosen',
                'bio' => $role === 'kaprodi'
                    ? 'Ketua program studi ' . $prodi . ' UMIBA.'
                    : 'Akun dosen PDDikti untuk program studi ' . $prodi . '.',
            ]);
        }

        $advisorByProdi = [];
        foreach ($dosenUsers as $user) {
            if (!isset($advisorByProdi[$user->prodi])) {
                $advisorByProdi[$user->prodi] = $user;
            }
        }

        foreach ($mahasiswaData as $index => $row) {
            $name = $normalize($row['nama'] ?? 'Mahasiswa ' . ($index + 1));
            $nim = trim((string) ($row['nim'] ?? ''));
            if ($nim === '') {
                $nim = 'MHS' . str_pad((string) ($index + 1), 6, '0', STR_PAD_LEFT);
            }
            $prodi = $normalize($row['nama_prodi'] ?? '-');
            $advisor = $advisorByProdi[$prodi] ?? $admin;
            $status = 'Aktif';
            $statusSaatIni = (string) ($row['status_saat_ini'] ?? '');
            if ($statusSaatIni !== '') {
                $lower = Str::lower($statusSaatIni);
                if (Str::contains($lower, ['lulus', 'graduate', 'yudisium'])) {
                    $status = 'Lulus';
                } elseif (Str::contains($lower, ['keluar', 'drop', 'nonaktif'])) {
                    $status = 'Keluar';
                }
            }

            User::create([
                'name' => $name,
                'email' => $slugifyEmail($name, '.mhs.' . $nim . '@umiba.ac.id'),
                'password' => Hash::make('password123'),
                'role' => 'mahasiswa',
                'nim_nip' => $nim,
                'prodi' => $prodi,
                'status' => $status,
                'dosen_wali_id' => $advisor->id,
                'bio' => 'Akun mahasiswa PDDikti untuk program studi ' . $prodi . '.',
            ]);
        }

        $this->command?->info('Seeded PDDikti users: ' . count($dosenUsers) . ' dosen/kaprodi dan ' . count($mahasiswaData) . ' mahasiswa.');
    }
}
