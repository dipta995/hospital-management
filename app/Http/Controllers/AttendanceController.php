<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Store or update attendance based on fingerprint and RFID match
     * Route: POST /attendance/mark
     */
    public function mark(Request $request)
    {
        $data = $request->validate([
            'rfid' => 'required|integer',
            'fingerprint_data' => 'nullable|string',
        ]);

        $employee = Employee::where('rfid', $data['rfid'])->first();
        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found for this RFID.'
            ], 404);
        }

        $today = Carbon::today();
        $now = Carbon::now();

        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            // First IN of the day
            $attendance = Attendance::create([
                'employee_id' => $employee->id,
                'fingerprint_data' => $data['fingerprint_data'] ?? null,
                'date' => $today,
                'in_time' => $now,
                'out_time' => null,
            ]);
            $message = 'Attendance IN marked.';
        } else {
            // Update OUT time
            $attendance->out_time = $now;
            $attendance->save();
            $message = 'Attendance OUT updated.';
        }

        return response()->json([
            'status' => true,
            'message' => $message,
            'attendance' => $attendance
        ]);
    }

    /**
     * Display attendance summary with filter and pagination
     * Route: GET /attendance
     */
    public function index(Request $request)
    {
        $month = $request->get('month', now()->format('F'));
        $year = $request->get('year', now()->year);
        $employeeId = $request->get('employee_id');

        $start = Carbon::parse("1 $month $year")->startOfMonth();
        $end = Carbon::parse("1 $month $year")->endOfMonth();

        $query = Attendance::with('employee')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()]);
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }
        $attendances = $query->orderBy('date', 'desc')->paginate(20);

        return view('backend.pages.attendance.index', compact('attendances', 'month', 'year', 'employeeId'));
    }
}
