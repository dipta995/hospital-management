<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
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

        $now = Carbon::now('Asia/Dhaka');
        $today = $now->toDateString();
        $mode = Setting::getByBranch($employee->branch_id, 'attendance_mode', 'standard');
        $isHourly = $mode === 'hourly';

        $openAttendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->where('mode', $isHourly ? 'hourly' : 'standard')
            ->whereNull('out_time')
            ->orderByDesc('id')
            ->first();

        if ($openAttendance) {
            $openAttendance->out_time = $now;
            $openAttendance->save();

            $attendance = $openAttendance;
            $message = 'Attendance OUT marked.';
        } else {
            $attendance = Attendance::create([
                'employee_id' => $employee->id,
                'fingerprint_data' => $data['fingerprint_data'] ?? null,
                'mode' => $isHourly ? 'hourly' : 'standard',
                'hour_slot' => (int) $now->format('G'),
                'date' => $today,
                'in_time' => $now,
                'out_time' => null,
            ]);
            $message = 'Attendance IN marked.';
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
        $export = $request->get('export');

        $start = Carbon::parse("1 $month $year")->startOfMonth();
        $end = Carbon::parse("1 $month $year")->endOfMonth();

        $query = Attendance::with('employee')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->whereHas('employee', function ($employeeQuery) {
                $employeeQuery->where('branch_id', auth()->user()->branch_id);
            });
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }
        $attendances = $query->orderBy('date', 'desc')->get();
        $groupedAttendances = $attendances->groupBy('date');

        if ($export === 'pdf') {
            $data = [
                'groupedAttendances' => $groupedAttendances,
                'month' => $month,
                'year' => $year,
                'employeeId' => $employeeId,
            ];

            return Pdf::loadView('backend.pages.attendance.sheet', $data)
                ->stream("attendance-{$month}-{$year}.pdf");
        }

        $employees = Employee::where('branch_id', auth()->user()->branch_id)->orderBy('name')->get();

        return view('backend.pages.attendance.index', compact('groupedAttendances', 'month', 'year', 'employeeId', 'employees'));
    }

    /**
     * Manually create an attendance record
     * Route: POST /admin/attendance
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date'        => 'required|date',
            'in_time'     => 'required|date_format:H:i',
            'out_time'    => 'nullable|date_format:H:i|after_or_equal:in_time',
            'note'        => 'nullable|string|max:500',
        ]);

        $employee = Employee::findOrFail($data['employee_id']);

        if ((int) $employee->branch_id !== (int) auth()->user()->branch_id) {
            abort(403, 'You are not allowed to add attendance for this employee.');
        }

        $date       = Carbon::parse($data['date'])->toDateString();
        $inDateTime = Carbon::parse($date . ' ' . $data['in_time'])->toDateTimeString();
        $outDateTime = !empty($data['out_time'])
            ? Carbon::parse($date . ' ' . $data['out_time'])->toDateTimeString()
            : null;

        Attendance::create([
            'employee_id' => $employee->id,
            'mode'        => 'standard',
            'hour_slot'   => 0,
            'date'        => $date,
            'in_time'     => $inDateTime,
            'out_time'    => $outDateTime,
            'note'        => $data['note'] ?? null,
        ]);

        return back()->with('success', 'Attendance record added successfully.');
    }

    public function updateTime(Request $request, Attendance $attendance)
    {
        if (!$attendance->employee || (int) $attendance->employee->branch_id !== (int) auth()->user()->branch_id) {
            abort(403, 'You are not allowed to modify this attendance record.');
        }

        $data = $request->validate([
            'date' => 'required|date',
            'in_time' => 'required|date_format:H:i',
            'out_time' => 'nullable|date_format:H:i|after_or_equal:in_time',
            'note' => 'nullable|string|max:500',
        ]);

        $attendanceDate = Carbon::parse($data['date'])->toDateString();
        $inDateTime = Carbon::parse($attendanceDate . ' ' . $data['in_time'])->toDateTimeString();
        $outDateTime = null;

        if (!empty($data['out_time'])) {
            $outDateTime = Carbon::parse($attendanceDate . ' ' . $data['out_time'])->toDateTimeString();
        }

        $attendance->update([
            'date' => $attendanceDate,
            'in_time' => $inDateTime,
            'out_time' => $outDateTime,
            'hour_slot' => $attendance->mode === 'hourly' ? (int) Carbon::parse($inDateTime)->format('G') : 0,
            'note' => $data['note'] ?? null,
        ]);

        return back()->with('success', 'Attendance time updated successfully.');
    }
}
