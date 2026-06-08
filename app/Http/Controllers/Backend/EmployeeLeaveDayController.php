<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeLeaveDay;
use App\Services\EmployeeAttendanceSummaryService;
use App\Services\HrSchemaService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmployeeLeaveDayController extends Controller
{
    public function __construct(
        private EmployeeAttendanceSummaryService $summaryService,
        private HrSchemaService $hrSchemaService
    ) {
        $this->checkGuard();
    }

    public function index(Request $request, $employeeId)
    {
        $this->checkOwnPermission('employees.index');
        $this->ensureSchemaInstalled();

        $employee = $this->findEmployee($employeeId);
        $month = $request->get('month', now()->format('F'));
        $year = $request->get('year', now()->format('Y'));

        $monthDate = Carbon::createFromFormat('F Y', "$month $year");
        $leaveDays = EmployeeLeaveDay::where('employee_id', $employee->id)
            ->whereBetween('date', [
                $monthDate->copy()->startOfMonth()->toDateString(),
                $monthDate->copy()->endOfMonth()->toDateString(),
            ])
            ->orderBy('date')
            ->get();

        $summary = $this->summaryService->summarize($employee, $month, $year);

        return view('backend.pages.employees.leave-days', [
            'employee' => $employee,
            'leaveDays' => $leaveDays,
            'summary' => $summary,
            'month' => $month,
            'year' => $year,
            'leaveTypes' => EmployeeLeaveDay::TYPES,
            'weekDays' => EmployeeAttendanceSummaryService::WEEK_DAYS,
            'pageHeader' => [
                'title' => 'Employee Leave & Off Days',
                'singular_name' => 'Employee',
            ],
        ]);
    }

    public function store(Request $request, $employeeId)
    {
        $this->checkOwnPermission('employees.edit');
        $this->ensureSchemaInstalled();

        $employee = $this->findEmployee($employeeId);

        $data = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:' . implode(',', array_keys(EmployeeLeaveDay::TYPES)),
            'reason' => 'nullable|string|max:500',
            'is_paid' => 'nullable|boolean',
        ]);

        $date = Carbon::parse($data['date'])->toDateString();
        $weeklyOffDays = $this->summaryService->normalizeWeeklyOffDays($employee->weekly_off_days ?? []);
        $dayName = EmployeeAttendanceSummaryService::WEEK_DAYS[Carbon::parse($date)->dayOfWeek];

        if (in_array($dayName, $weeklyOffDays, true)) {
            return RedirectHelper::backWithInput('<strong>Notice:</strong> This date is already a weekly off day for this employee.');
        }

        EmployeeLeaveDay::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'date' => $date,
            ],
            [
                'branch_id' => $employee->branch_id,
                'type' => $data['type'],
                'reason' => $data['reason'] ?? null,
                'is_paid' => $request->boolean('is_paid', true),
                'created_by' => auth()->id(),
            ]
        );

        return RedirectHelper::back('<strong>Success!</strong> Leave day saved successfully.');
    }

    public function destroy($employeeId, $leaveDayId)
    {
        $this->checkOwnPermission('employees.edit');
        $this->ensureSchemaInstalled();

        $employee = $this->findEmployee($employeeId);
        $leaveDay = EmployeeLeaveDay::where('employee_id', $employee->id)->findOrFail($leaveDayId);
        $leaveDay->delete();

        return RedirectHelper::back('<strong>Success!</strong> Leave day removed successfully.');
    }

    public function updateSchedule(Request $request, $employeeId)
    {
        $this->checkOwnPermission('employees.edit');
        $this->ensureSchemaInstalled();

        $employee = $this->findEmployee($employeeId);

        $data = $request->validate([
            'weekly_off_days' => 'nullable|array',
            'weekly_off_days.*' => 'in:' . implode(',', EmployeeAttendanceSummaryService::WEEK_DAYS),
            'working_hours_per_day' => 'nullable|numeric|min:1|max:24',
            'annual_leave_quota' => 'nullable|integer|min:0|max:365',
        ]);

        $employee->weekly_off_days = $this->summaryService->normalizeWeeklyOffDays($data['weekly_off_days'] ?? []);
        $employee->working_hours_per_day = $data['working_hours_per_day'] ?? 8;
        $employee->annual_leave_quota = $data['annual_leave_quota'] ?? 12;
        $employee->save();

        return RedirectHelper::back('<strong>Success!</strong> Employee schedule updated successfully.');
    }

    private function findEmployee($employeeId): Employee
    {
        return Employee::where('branch_id', auth()->user()->branch_id)->findOrFail($employeeId);
    }

    private function ensureSchemaInstalled(): void
    {
        if (!$this->hrSchemaService->isInstalled()) {
            abort(503, 'HR schedule schema is not installed. Ask Super Admin to install it from the dashboard.');
        }
    }
}
