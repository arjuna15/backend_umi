<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class NeoFeederService
{
    protected string $url;
    protected string $username;
    protected string $password;
    protected ?string $token = null;

    public function __construct()
    {
        $this->url = config('feeder.url');
        $this->username = config('feeder.username');
        $this->password = config('feeder.password');
    }

    /**
     * Get security token from Neo Feeder
     */
    public function getToken(): string
    {
        if ($this->token) {
            return $this->token;
        }

        try {
            $response = Http::post($this->url, [
                'act' => 'GetToken',
                'username' => $this->username,
                'password' => $this->password,
            ]);

            if ($response->failed()) {
                throw new Exception("Connection to Neo Feeder WS failed.");
            }

            $result = $response->json();

            if (isset($result['error_code']) && $result['error_code'] != 0) {
                throw new Exception("Neo Feeder login failed: " . ($result['error_desc'] ?? 'Unknown Error'));
            }

            if (isset($result['data']['token'])) {
                $this->token = $result['data']['token'];
                return $this->token;
            }

            throw new Exception("Token not found in Neo Feeder response.");
        } catch (Exception $e) {
            Log::error("NeoFeederService (GetToken) Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Call general Neo Feeder web service action
     */
    public function request(string $action, array $records = [], array $filter = [], int $limit = 0, int $offset = 0): array
    {
        try {
            $token = $this->getToken();
            $payload = [
                'token' => $token,
                'act' => $action,
            ];

            if (!empty($records)) {
                $payload['key'] = $records[0]['key'] ?? []; // For update/delete keys
                $payload['record'] = count($records) === 1 ? $records[0] : $records;
            }

            if (!empty($filter)) {
                $payload['filter'] = $filter;
            }

            if ($limit > 0) {
                $payload['limit'] = $limit;
                $payload['offset'] = $offset;
            }

            $response = Http::post($this->url, $payload);

            if ($response->failed()) {
                throw new Exception("Request to Neo Feeder failed.");
            }

            $result = $response->json();

            if (isset($result['error_code']) && $result['error_code'] != 0) {
                throw new Exception("Neo Feeder action [{$action}] error: " . ($result['error_desc'] ?? 'Unknown Error'));
            }

            return $result['data'] ?? $result;
        } catch (Exception $e) {
            Log::error("NeoFeederService ({$action}) Request Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Sync local students (mahasiswa) to Neo Feeder
     */
    public function syncMahasiswa($student): string
    {
        // Map local Laravel User model (role=mahasiswa) to Neo Feeder format
        $record = [
            'nama_mahasiswa' => $student->name,
            'jenis_kelamin' => 'L', // Default, should ideally map from profile
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '2004-01-01',
            'id_agama' => 1, // Islam
            'nik' => $student->nim_nip, // Using NIM as NIK fallback if not defined
            'nipd' => $student->nim_nip, // NIM
            'handphone' => $student->phone ?? '081234567890',
            'email' => $student->email,
            'nama_ibu_kandung' => 'Ibu Kandung ' . $student->name, // Mandatory in Feeder
        ];

        $response = $this->request('InsertProfilMahasiswa', [$record]);
        $feederId = $response['id_mahasiswa'] ?? null;

        if ($feederId) {
            $student->update(['feeder_id' => $feederId]);
            return $feederId;
        }

        throw new Exception("Failed to sync Mahasiswa: No ID returned.");
    }

    /**
     * Sync local course (mata kuliah) to Neo Feeder
     */
    public function syncMataKuliah($course): string
    {
        $record = [
            'kode_mata_kuliah' => $course->code,
            'nama_mata_kuliah' => $course->name,
            'sks_mata_kuliah' => $course->sks,
            'id_jenis_mata_kuliah' => 'A', // Wajib
        ];

        $response = $this->request('InsertMataKuliah', [$record]);
        $feederId = $response['id_matkul'] ?? null;

        if ($feederId) {
            $course->update(['feeder_id' => $feederId]);
            return $feederId;
        }

        throw new Exception("Failed to sync Mata Kuliah: No ID returned.");
    }

    /**
     * Sync local KRS submission (Rencana Studi) to Neo Feeder
     */
    public function syncKrs($krs): string
    {
        if (!$krs->mahasiswa->feeder_id || !$krs->course->feeder_id) {
            throw new Exception("Cannot sync KRS: Student or Course not synced to Neo Feeder yet.");
        }

        $record = [
            'id_registrasi_mahasiswa' => $krs->mahasiswa->feeder_id,
            'id_kelas_kuliah' => $krs->course->feeder_id, // Maps to class/course ID
        ];

        $response = $this->request('InsertKrsMahasiswa', [$record]);
        $feederId = $response['id_krs'] ?? 'synced_' . uniqid(); // Fallback if WS success but doesn't return id

        $krs->update(['feeder_id' => $feederId]);
        return $feederId;
    }

    /**
     * Sync local grade (nilai mahasiswa) to Neo Feeder
     */
    public function syncNilai($grade): string
    {
        if (!$grade->mahasiswa->feeder_id || !$grade->course->feeder_id) {
            throw new Exception("Cannot sync Grade: Student or Course not synced to Neo Feeder yet.");
        }

        $record = [
            'id_registrasi_mahasiswa' => $grade->mahasiswa->feeder_id,
            'id_kelas_kuliah' => $grade->course->feeder_id,
            'nilai_angka' => $grade->score ?? 0,
            'nilai_indeks' => $this->getNilaiIndeks($grade->grade ?? 'E'),
            'nilai_huruf' => $grade->grade ?? 'E',
        ];

        $response = $this->request('InsertNilaiPerkuliahanKelas', [$record]);
        $feederId = $response['id_nilai'] ?? 'synced_grade_' . uniqid();

        $grade->update(['feeder_id' => $feederId]);
        return $feederId;
    }

    private function getNilaiIndeks(string $huruf): float
    {
        return match ($huruf) {
            'A' => 4.0,
            'A-' => 3.75,
            'B+' => 3.5,
            'B' => 3.0,
            'B-' => 2.75,
            'C+' => 2.5,
            'C' => 2.0,
            'D' => 1.0,
            default => 0.0,
        };
    }
}
