<?php

namespace App\Http\Controllers\Siakad;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\ScheduleOverride;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Get a unified chronological calendar view of weekly schedules and overrides for a given month.
     */
    public function getCalendarView(Request $request)
    {
        $year = $request->query('year');
        $month = $request->query('month');

        if ($year && $month) {
            $monthStr = sprintf('%04d-%02d', $year, $month);
        } else {
            $monthStr = $request->query('month', Carbon::now()->format('Y-m'));
            if (is_numeric($monthStr) && (int)$monthStr >= 1 && (int)$monthStr <= 12) {
                $monthStr = Carbon::now()->year . '-' . str_pad($monthStr, 2, '0', STR_PAD_LEFT);
            }
        }
        
        try {
            $startDate = Carbon::parse($monthStr)->startOfMonth();
            $endDate = Carbon::parse($monthStr)->endOfMonth();
        } catch (\Exception $e) {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }

        // Get all courses with their lecturers
        $courses = Course::with('dosen')->get();

        // Get all overrides where override_date is in the month or new_date is in the month
        $overrides = ScheduleOverride::with(['originalSchedule.dosen', 'swappedWithSchedule.dosen'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('override_date', [$startDate, $endDate])
                      ->orWhereBetween('new_date', [$startDate, $endDate]);
            })
            ->get();

        $events = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $dayName = $date->locale('id')->translatedFormat('l'); // 'Senin', 'Selasa', etc.

            // Get courses normally scheduled on this day of the week
            $normalCourses = $courses->filter(function ($course) use ($dayName) {
                return strcasecmp($course->hari ?? '', $dayName) === 0;
            });

            foreach ($normalCourses as $course) {
                // Check if there is an override for this course on this date
                $override = $overrides->first(function ($ov) use ($course, $dateStr) {
                    return $ov->original_schedule_id == $course->id && $ov->override_date->format('Y-m-d') === $dateStr;
                });

                if (!$override) {
                    // No override, normal class runs
                    $events[] = [
                        'date' => $dateStr,
                        'course_id' => $course->id,
                        'course_name' => $course->name,
                        'dosen' => $course->dosen?->name ?? '-',
                        'time' => trim(($course->jam_mulai ?? '') . ($course->jam_selesai ? ' - ' . $course->jam_selesai : '')),
                        'room' => $course->ruang ?? '-',
                        'status' => 'normal',
                        'notes' => null,
                    ];
                } else {
                    // There is an override for this original schedule on this date
                    if ($override->status === 'cancelled') {
                        $events[] = [
                            'date' => $dateStr,
                            'course_id' => $course->id,
                            'course_name' => $course->name,
                            'dosen' => $course->dosen?->name ?? '-',
                            'time' => trim(($course->jam_mulai ?? '') . ($course->jam_selesai ? ' - ' . $course->jam_selesai : '')),
                            'room' => $course->ruang ?? '-',
                            'status' => 'cancelled',
                            'notes' => $override->notes,
                        ];
                    } elseif ($override->status === 'moved') {
                        $events[] = [
                            'date' => $dateStr,
                            'course_id' => $course->id,
                            'course_name' => $course->name,
                            'dosen' => $course->dosen?->name ?? '-',
                            'time' => trim(($course->jam_mulai ?? '') . ($course->jam_selesai ? ' - ' . $course->jam_selesai : '')),
                            'room' => $course->ruang ?? '-',
                            'status' => 'moved',
                            'notes' => $override->notes ?: 'Moved to ' . ($override->new_date ? $override->new_date->format('Y-m-d') : '') . ' ' . $override->new_time,
                        ];
                    } elseif ($override->status === 'swapped') {
                        $swappedCourse = $override->swappedWithSchedule;
                        if ($swappedCourse) {
                            $events[] = [
                                'date' => $dateStr,
                                'course_id' => $swappedCourse->id,
                                'course_name' => $swappedCourse->name,
                                'dosen' => $swappedCourse->dosen?->name ?? '-',
                                'time' => trim(($course->jam_mulai ?? '') . ($course->jam_selesai ? ' - ' . $course->jam_selesai : '')),
                                'room' => $course->ruang ?? '-',
                                'status' => 'swapped',
                                'notes' => 'Swapped with ' . $course->name . '. ' . ($override->notes ?? ''),
                            ];
                        }
                    }
                }
            }

            // Also check for any classes that were moved or swapped TO this date (as a new date/time)
            $incomingOverrides = $overrides->filter(function ($ov) use ($dateStr) {
                return $ov->new_date && $ov->new_date->format('Y-m-d') === $dateStr;
            });

            foreach ($incomingOverrides as $override) {
                $originalCourse = $override->originalSchedule;
                if ($originalCourse) {
                    if ($override->status === 'moved') {
                        $events[] = [
                            'date' => $dateStr,
                            'course_id' => $originalCourse->id,
                            'course_name' => $originalCourse->name,
                            'dosen' => $originalCourse->dosen?->name ?? '-',
                            'time' => $override->new_time ? substr($override->new_time, 0, 5) : trim(($originalCourse->jam_mulai ?? '') . ($originalCourse->jam_selesai ? ' - ' . $originalCourse->jam_selesai : '')),
                            'room' => $originalCourse->ruang ?? '-',
                            'status' => 'moved_here',
                            'notes' => 'Moved from ' . $override->override_date->format('Y-m-d') . '. ' . ($override->notes ?? ''),
                        ];
                    } elseif ($override->status === 'swapped') {
                        $events[] = [
                            'date' => $dateStr,
                            'course_id' => $originalCourse->id,
                            'course_name' => $originalCourse->name,
                            'dosen' => $originalCourse->dosen?->name ?? '-',
                            'time' => $override->new_time ? substr($override->new_time, 0, 5) : trim(($originalCourse->jam_mulai ?? '') . ($originalCourse->jam_selesai ? ' - ' . $originalCourse->jam_selesai : '')),
                            'room' => $originalCourse->ruang ?? '-',
                            'status' => 'swapped_here',
                            'notes' => 'Swapped from ' . $override->override_date->format('Y-m-d') . '. ' . ($override->notes ?? ''),
                        ];
                    }
                }
            }
        }

        // Sort events chronologically by date and start time
        usort($events, function ($a, $b) {
            if ($a['date'] === $b['date']) {
                return strcmp($a['time'], $b['time']);
            }
            return strcmp($a['date'], $b['date']);
        });

        return response()->json($events);
    }

    /**
     * Create a new schedule swap/override.
     */
    public function createOverride(Request $request)
    {
        $validated = $request->validate([
            'original_schedule_id' => 'required|exists:courses,id',
            'override_date' => 'required|date',
            'status' => 'required|in:swapped,cancelled,moved',
            'swapped_with_schedule_id' => 'nullable|required_if:status,swapped|exists:courses,id',
            'new_date' => 'nullable|required_if:status,moved,swapped|date',
            'new_time' => 'nullable|required_if:status,moved,swapped',
            'notes' => 'nullable|string',
        ]);

        $override = ScheduleOverride::create($validated);

        return response()->json([
            'message' => 'Schedule override created successfully',
            'override' => $override->load(['originalSchedule', 'swappedWithSchedule']),
        ], 201);
    }
}
