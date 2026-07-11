<?php

namespace App\Jobs;

use App\Models\Course;
use App\Models\Grade;
use App\Models\KrsSubmission;
use App\Models\User;
use App\Services\NeoFeederService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;

class SyncToFeederJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $type; // 'mahasiswa', 'courses', 'krs', 'grades'
    protected array $ids;   // Local database IDs to sync

    /**
     * Create a new job instance.
     */
    public function __construct(string $type, array $ids)
    {
        $this->type = $type;
        $this->ids = $ids;
    }

    /**
     * Execute the job.
     */
    public function handle(NeoFeederService $feederService): void
    {
        Log::info("Starting Feeder Sync Job: [{$this->type}] for " . count($this->ids) . " items.");

        try {
            switch ($this->type) {
                case 'mahasiswa':
                    $students = User::where('role', 'mahasiswa')->whereIn('id', $this->ids)->get();
                    foreach ($students as $student) {
                        try {
                            $feederService->syncMahasiswa($student);
                            Log::info("Synced student: {$student->name} ({$student->nim_nip})");
                        } catch (Exception $e) {
                            Log::error("Failed syncing student [{$student->id}]: " . $e->getMessage());
                        }
                    }
                    break;

                case 'courses':
                    $courses = Course::whereIn('id', $this->ids)->get();
                    foreach ($courses as $course) {
                        try {
                            $feederService->syncMataKuliah($course);
                            Log::info("Synced course: {$course->name} ({$course->code})");
                        } catch (Exception $e) {
                            Log::error("Failed syncing course [{$course->id}]: " . $e->getMessage());
                        }
                    }
                    break;

                case 'krs':
                    $krsItems = KrsSubmission::whereIn('id', $this->ids)->get();
                    foreach ($krsItems as $krs) {
                        try {
                            $feederService->syncKrs($krs);
                            Log::info("Synced KRS record for student: {$krs->mahasiswa->name}");
                        } catch (Exception $e) {
                            Log::error("Failed syncing KRS [{$krs->id}]: " . $e->getMessage());
                        }
                    }
                    break;

                case 'grades':
                    $grades = Grade::whereIn('id', $this->ids)->get();
                    foreach ($grades as $grade) {
                        try {
                            $feederService->syncNilai($grade);
                            Log::info("Synced grade record: Student ID [{$grade->mahasiswa_id}]");
                        } catch (Exception $e) {
                            Log::error("Failed syncing grade [{$grade->id}]: " . $e->getMessage());
                        }
                    }
                    break;

                default:
                    Log::warning("Unknown feeder sync type: {$this->type}");
            }
        } catch (Exception $e) {
            Log::error("Critical Error in SyncToFeederJob: " . $e->getMessage());
            $this->fail($e);
        }
    }
}
