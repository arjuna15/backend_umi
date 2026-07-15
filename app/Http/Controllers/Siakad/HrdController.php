<?php

namespace App\Http\Controllers\Siakad;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HrdController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('user');

        if ($request->has('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('nip', 'like', "%{$s}%");
            });
        }

        if ($request->has('department')) {
            $query->where('department', $request->department);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $employees = $query->orderBy('name')->paginate($request->get('per_page', 15));

        return response()->json(['data' => $employees]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'nip' => 'required|string|unique:employees,nip',
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'employment_type' => 'required|in:pns,kontrak,honorer',
            'join_date' => 'required|date',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'salary' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:active,inactive,retired',
        ]);

        $employee = Employee::create($validated);

        return response()->json(['data' => $employee], 201);
    }

    public function update($id, Request $request)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'nip' => 'sometimes|string|unique:employees,nip,' . $id,
            'name' => 'sometimes|string|max:255',
            'position' => 'sometimes|string|max:255',
            'department' => 'sometimes|string|max:255',
            'employment_type' => 'sometimes|in:pns,kontrak,honorer',
            'join_date' => 'sometimes|date',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'salary' => 'nullable|numeric|min:0',
            'status' => 'sometimes|in:active,inactive,retired',
        ]);

        $employee->update($validated);

        return response()->json(['data' => $employee]);
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return response()->json(['message' => 'Employee deleted']);
    }

    public function attendance(Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);

        $records = EmployeeAttendance::with('employee')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->get();

        return response()->json(['data' => $records]);
    }

    public function markAttendance(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:present,absent,late,leave,sick',
            'notes' => 'nullable|string',
        ]);

        $attendance = EmployeeAttendance::updateOrCreate(
            ['employee_id' => $validated['employee_id'], 'date' => $validated['date']],
            $validated
        );

        return response()->json(['data' => $attendance], 201);
    }

    public function stats()
    {
        $totalActive = Employee::where('status', 'active')->count();
        $byDepartment = Employee::where('status', 'active')
            ->selectRaw('department, count(*) as total')
            ->groupBy('department')
            ->pluck('total', 'department');

        $now = Carbon::now();
        $totalWorkdays = EmployeeAttendance::whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->count();
        $presentDays = EmployeeAttendance::whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->whereIn('status', ['present', 'late'])
            ->count();
        $attendanceRate = $totalWorkdays > 0 ? round(($presentDays / $totalWorkdays) * 100, 2) : 0;

        return response()->json(['data' => [
            'total_active' => $totalActive,
            'by_department' => $byDepartment,
            'attendance_rate_this_month' => $attendanceRate,
        ]]);
    }
}
