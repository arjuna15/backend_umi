const fs = require('fs');
const path = require('path');

// 1. Update the migration file
const migrationsDir = './database/migrations';
const files = fs.readdirSync(migrationsDir);
const migrationFile = files.find(f => f.includes('add_schedule_columns_to_courses_table'));
if (migrationFile) {
    const filePath = path.join(migrationsDir, migrationFile);
    let content = fs.readFileSync(filePath, 'utf8');
    content = content.replace('public function up(): void\n    {', `public function up(): void\n    {\n        Schema::table('courses', function (Blueprint $table) {\n            $table->string('hari')->nullable();\n            $table->string('jam_mulai')->nullable();\n            $table->string('jam_selesai')->nullable();\n            $table->string('ruang')->nullable();\n        });`);
    content = content.replace('public function down(): void\n    {', `public function down(): void\n    {\n        Schema::table('courses', function (Blueprint $table) {\n            $table->dropColumn(['hari', 'jam_mulai', 'jam_selesai', 'ruang']);\n        });`);
    fs.writeFileSync(filePath, content);
}

// 2. Update routes/api.php
const routesPath = './routes/api.php';
let routesContent = fs.readFileSync(routesPath, 'utf8');
if (!routesContent.includes('/courses/{id}/schedule')) {
    routesContent = routesContent.replace("Route::post('/courses/{id}/plot', [\\App\\Http\\Controllers\\SiakadController::class, 'plotDosen']);", "Route::post('/courses/{id}/plot', [\\App\\Http\\Controllers\\SiakadController::class, 'plotDosen']);\n        Route::post('/courses/{id}/schedule', [\\App\\Http\\Controllers\\SiakadController::class, 'plotSchedule']);");
    fs.writeFileSync(routesPath, routesContent);
}

// 3. Update SiakadController.php
const controllerPath = './app/Http/Controllers/SiakadController.php';
let controllerContent = fs.readFileSync(controllerPath, 'utf8');
if (!controllerContent.includes('public function plotSchedule')) {
    // Add plotSchedule right after plotDosen
    const plotDosenEnd = "return response()->json(['message' => 'Dosen assigned successfully']);\n    }";
    const plotScheduleCode = `\n\n    public function plotSchedule(Request $request, $id)\n    {\n        $request->validate([\n            'hari' => 'required|string',\n            'jamMulai' => 'required|string',\n            'jamSelesai' => 'required|string',\n            'ruang' => 'required|string',\n        ]);\n        $course = Course::findOrFail($id);\n        $course->update([\n            'hari' => $request->hari,\n            'jam_mulai' => $request->jamMulai,\n            'jam_selesai' => $request->jamSelesai,\n            'ruang' => $request->ruang,\n        ]);\n        return response()->json(['message' => 'Schedule updated successfully', 'course' => $course]);\n    }`;
    controllerContent = controllerContent.replace(plotDosenEnd, plotDosenEnd + plotScheduleCode);
    
    // Update getDosenDashboard
    const getDosenDashboardOld = "'time' => '10:00 - 12:30',\n                'room' => 'Lab Komputer 1',";
    const getDosenDashboardNew = "'time' => ($course->jam_mulai && $course->jam_selesai) ? $course->jam_mulai . ' - ' . $course->jam_selesai : 'Belum diatur',\n                'room' => $course->ruang ?? 'Belum ada ruang',";
    controllerContent = controllerContent.replace(getDosenDashboardOld, getDosenDashboardNew);
    
    // Also fix getKaprodiCourses to map the database columns properly if needed, but Eloquent returns them directly.
    // However, JS uses camelCase (jamMulai, jamSelesai) instead of snake_case (jam_mulai).
    // Let's modify the map inside getKaprodiCourses so frontend doesn't break
    const kaprodiCoursesOld = "$courses = Course::with('dosen')->get();\n        $dosens = User::where('role', 'dosen')->get();";
    const kaprodiCoursesNew = "$courses = Course::with('dosen')->get()->map(function($course) {\n            $course->jamMulai = $course->jam_mulai;\n            $course->jamSelesai = $course->jam_selesai;\n            return $course;\n        });\n        $dosens = User::where('role', 'dosen')->get();";
    controllerContent = controllerContent.replace(kaprodiCoursesOld, kaprodiCoursesNew);

    fs.writeFileSync(controllerPath, controllerContent);
}

console.log('Backend patched successfully!');
