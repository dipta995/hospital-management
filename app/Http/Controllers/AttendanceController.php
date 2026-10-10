<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Setting;
use App\Services\EmployeeAttendanceSummaryService;
use App\Services\AttendancePunchService;
use App\Services\AttendanceRepairService;
use App\Services\AttendanceSchemaService;
use App\Services\HrSchemaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function __construct(
        private EmployeeAttendanceSummaryService $summaryService,
        private HrSchemaService $hrSchemaService,
        private AttendancePunchService $punchService
    ) {
    }

    /**
     * IN/OUT datetimes for a manual entry. An OUT earlier than IN means the duty ended the next day.
     */
    private function manualTimes(string $date, string $inTime, ?string $outTime): array
    {
        $in = Carbon::parse($date . ' ' . $inTime, 'Asia/Dhaka');
        if (!$outTime) {
            return [$in, null];
        }

        $out = Carbon::parse($date . ' ' . $outTime, 'Asia/Dhaka');
        if ($out->lte($in)) {
            $out->addDay();
        }

        return [$in, $out];
    }

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

        $employee = Employee::employedSince(Carbon::now('Asia/Dhaka'))->where('rfid', $data['rfid'])->first();
        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found for this RFID.'
            ], 404);
        }

        $punch = $this->punchService->punch($employee, 'rfid', $data['fingerprint_data'] ?? null);

        return response()->json([
            'status' => true,
            'result' => $punch['result'],
            'message' => $punch['message'],
            'attendance' => $punch['attendance'],
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

        $employees = Employee::where('branch_id', auth()->user()->branch_id)
            ->employedSince($start)
            ->orderBy('name')
            ->get();
        $hrSchemaInstalled = $this->hrSchemaService->isInstalled();
        $canSummarizeAttendance = $this->hrSchemaService->canSummarizeAttendance();
        $employeeSummaries = [];

        if ($canSummarizeAttendance) {
            $summaryEmployees = $employeeId
                ? $employees->where('id', (int) $employeeId)
                : $employees;
            $employeeSummaries = $this->summaryService->summarizeMany($summaryEmployees, $month, (string) $year);
        }

        if ($export === 'pdf') {
            $data = [
                'groupedAttendances' => $groupedAttendances,
                'month' => $month,
                'year' => $year,
                'employeeId' => $employeeId,
                'employees' => $employees,
                'employeeSummaries' => $employeeSummaries,
                'hrSchemaInstalled' => $hrSchemaInstalled,
                'canSummarizeAttendance' => $canSummarizeAttendance,
            ];

            return Pdf::loadView('backend.pages.attendance.sheet', $data)
                ->stream("attendance-{$month}-{$year}.pdf");
        }

        return view('backend.pages.attendance.index', compact(
            'groupedAttendances',
            'month',
            'year',
            'employeeId',
            'employees',
            'employeeSummaries',
            'hrSchemaInstalled',
            'canSummarizeAttendance'
        ));
    }

    /**
     * Daily attendance sheet: every employee with status, in/out and worked time for one date
     * Route: GET /admin/attendance/daily
     */
    public function daily(Request $request)
    {
        abort_unless(auth('admin')->user()?->can('employees.index'), 403, 'Unauthorized Access');

        $request->validate(['date' => 'nullable|date']);
        $date = $request->filled('date')
            ? Carbon::parse($request->get('date'), 'Asia/Dhaka')->startOfDay()
            : Carbon::now('Asia/Dhaka')->startOfDay();
        $status = $request->get('status');

        $employees = Employee::where('branch_id', auth()->user()->branch_id)
            ->where(fn ($q) => $q->whereNull('status')->orWhereNotIn('status', ['Inactive', 'inactive', '0']))
            ->employedSince($date)
            ->orderBy('name')
            ->get();

        $sheet = $this->summaryService->dailySheet($employees, $date);
        if ($status) {
            $sheet['rows'] = array_values(array_filter($sheet['rows'], fn ($row) => $row['status'] === $status));
        }

        $data = compact('sheet', 'date', 'status', 'employees');

        if ($request->get('export') === 'pdf') {
            return Pdf::loadView('backend.pages.attendance.daily-sheet', $data)
                ->stream('daily-attendance-' . $date->toDateString() . '.pdf');
        }

        return view('backend.pages.attendance.daily', $data);
    }

    /**
     * Preview of old device punches re-paired with the night-duty rules.
     * Route: GET /admin/attendance/repair
     */
    public function repair(Request $request, AttendanceRepairService $repairService)
    {
        abort_unless(auth('admin')->user()?->can('employees.edit'), 403, 'Unauthorized Access');

        [$month, $year, $start, $end] = $this->repairRange($request);
        $employees = $this->repairEmployees($start);
        $plans = $repairService->plan($employees, $start, $end);

        return view('backend.pages.attendance.repair', compact('month', 'year', 'plans', 'employees'));
    }

    /**
     * Route: POST /admin/attendance/repair
     */
    public function applyRepair(Request $request, AttendanceRepairService $repairService)
    {
        abort_unless(auth('admin')->user()?->can('employees.edit'), 403, 'Unauthorized Access');

        $request->validate(['employee_ids' => 'required|array|min:1', 'employee_ids.*' => 'integer']);
        [$month, $year, $start, $end] = $this->repairRange($request);

        $employees = $this->repairEmployees($start)->whereIn('id', array_map('intval', $request->input('employee_ids')));
        $fixedEmployees = 0;
        $sessions = 0;
        foreach ($employees as $employee) {
            $plan = $repairService->planFor($employee, $start, $end);
            if ($plan) {
                $sessions += $repairService->apply($plan);
                $fixedEmployees++;
            }
        }

        return redirect()->route('admin.attendance.repair', ['month' => $month, 'year' => $year])
            ->with('success', "Fixed punches of {$fixedEmployees} employee(s); {$sessions} duty record(s) rebuilt for {$month} {$year}.");
    }

    private function repairRange(Request $request): array
    {
        $month = $request->get('month', now('Asia/Dhaka')->format('F'));
        $year = (int) $request->get('year', now('Asia/Dhaka')->year);
        $start = Carbon::parse("1 $month $year", 'Asia/Dhaka')->startOfMonth();

        return [$start->format('F'), $year, $start, $start->copy()->endOfMonth()];
    }

    private function repairEmployees(Carbon $start)
    {
        return Employee::where('branch_id', auth()->user()->branch_id)
            ->employedSince($start)
            ->orderBy('name')
            ->get();
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
            'out_time'    => 'nullable|date_format:H:i|different:in_time',
            'note'        => 'nullable|string|max:500',
        ]);

        $employee = Employee::findOrFail($data['employee_id']);

        if ((int) $employee->branch_id !== (int) auth()->user()->branch_id) {
            abort(403, 'You are not allowed to add attendance for this employee.');
        }

        if (!$employee->wasEmployedOn($data['date'])) {
            return back()->withInput()->with('error', $employee->name . ' resigned on '
                . optional($employee->resigned_at)->format('d M Y') . '. Attendance cannot be added after that date.');
        }

        $date = Carbon::parse($data['date'])->toDateString();
        [$in, $out] = $this->manualTimes($date, $data['in_time'], $data['out_time'] ?? null);

        $attributes = [
            'employee_id' => $employee->id,
            'mode'        => 'standard',
            'hour_slot'   => 0,
            'date'        => $date,
            'in_time'     => $in->toDateTimeString(),
            'out_time'    => $out?->toDateTimeString(),
            'note'        => $data['note'] ?? null,
        ];
        if (AttendanceSchemaService::hasShifts()) {
            $attributes['shift_id'] = $employee->shift_id;
            $attributes['source'] = 'manual';
        }
        Attendance::create($attributes);

        return back()->with('success', 'Attendance record added successfully.'
            . ($out && $out->toDateString() !== $date ? ' (OUT on next day: ' . $out->format('d M h:i A') . ')' : ''));
    }

    public function updateTime(Request $request, Attendance $attendance)
    {
        if (!$attendance->employee || (int) $attendance->employee->branch_id !== (int) auth()->user()->branch_id) {
            abort(403, 'You are not allowed to modify this attendance record.');
        }

        $data = $request->validate([
            'date' => 'required|date',
            'in_time' => 'required|date_format:H:i',
            'out_time' => 'nullable|date_format:H:i|different:in_time',
            'note' => 'nullable|string|max:500',
        ]);

        $attendanceDate = Carbon::parse($data['date'])->toDateString();
        [$in, $out] = $this->manualTimes($attendanceDate, $data['in_time'], $data['out_time'] ?? null);

        $attendance->update([
            'date' => $attendanceDate,
            'in_time' => $in->toDateTimeString(),
            'out_time' => $out?->toDateTimeString(),
            'hour_slot' => $attendance->mode === 'hourly' ? (int) $in->format('G') : 0,
            'note' => $data['note'] ?? null,
        ]);

        return back()->with('success', 'Attendance time updated successfully.'
            . ($out && $out->toDateString() !== $attendanceDate ? ' (OUT on next day: ' . $out->format('d M h:i A') . ')' : ''));
    }
}
