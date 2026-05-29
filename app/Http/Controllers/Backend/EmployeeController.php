<?php

namespace App\Http\Controllers\Backend;

use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Cost;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.employees.index";
    public $create_route = "admin.employees.create";
    public $store_route = "admin.employees.store";
    public $edit_route = "admin.employees.edit";
    public $update_route = "admin.employees.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Employees",
            'sub_title' => "",
            'plural_name' => "employees",
            'singular_name' => "Employee",
            'index_route' => $this->index_route,
            'create_route' => $this->create_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/employees'),

        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->checkOwnPermission('employees.index');
        $data['pageHeader'] = $this->pageHeader;
        $currentMonth = Carbon::now()->format('F');
        $previousMonth = Carbon::now()->subMonth()->format('F');
        $currentYear = Carbon::now()->format('Y');
        $employees = Employee::with('employeeSalaries')->where('branch_id', auth()->user()->branch_id)
            ->orderBy('id', 'DESC')->paginate(20);

        $employees->getCollection()->transform(function ($employee) use ($previousMonth, $currentYear) {
            // Check if any salary record matches the current month and year
            $employee->salary_paid = $employee->employeeSalaries->contains(function ($salary) use ($previousMonth, $currentYear) {
                return $salary->month == $previousMonth && $salary->year == $currentYear;
            });
            return $employee;
        });
        $data['datas'] = $employees;

//        For Salary sheet only
        $emp = EmployeeSalary::with('employee')
            ->where('month', $request->month)
            ->where('year', $request->year);
        $employeeSalaries = $emp->get();
        $data['groupedSalaries'] = $employeeSalaries->groupBy(function ($salary) {
            return $salary->employee->name;
        });
        if ($request->query('month') != null && $request->query('month') != null) {
            return Pdf::loadView('backend.pages.employees.sheet', $data)->stream('sheet.pdf');


        }

        return view('backend.pages.employees.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->checkOwnPermission('employees.create');
        $data['pageHeader'] = $this->pageHeader;
        return view('backend.pages.employees.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        return $request;
        $this->checkOwnPermission('employees.create');
        $rules = [
            'name' => 'required|max:200',
            'phone' => 'required',
            'salary' => 'required',
            'rfid' => 'required|unique:employees,rfid',
        ];
        $request->validate($rules);
        try {
            $row = new Employee();
            $row->branch_id = auth()->user()->branch_id;
            $row->name = $request->name;
            $row->designation = $request->designation;
            $row->phone = $request->phone;
            $row->salary = $request->salary;
            $row->rfid = $request->rfid;

            if ($row->save()) {
                return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Employee Created Successfully');

            } else {
                return RedirectHelper::backWithInput();
            }
        } catch (QueryException $e) {
            return $e;
            return RedirectHelper::backWithInputFromException();
        }

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['pageHeader'] = $this->pageHeader;
        $data['singleData'] = Employee::where('branch_id', auth()->user()->branch_id)
            ->with('employeeSalaries')->find($id);
        return view('backend.pages.employees.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->checkOwnPermission('employees.edit');
        $data['pageHeader'] = $this->pageHeader;
        if ($data['edited'] = Employee::where('branch_id', auth()->user()->branch_id)
            ->find($id)) {
            return view('backend.pages.employees.edit', $data);
        } else {
            return RedirectHelper::backWithInputFromException();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->checkOwnPermission('employees.edit');


        $request->validate([
            'name' => 'required|max:200',
            'rfid' => 'required|unique:employees,rfid,' . $id,
        ]);
        try {
            if ($row = Employee::find($id)) {
                $row->branch_id = auth()->user()->branch_id;
                $row->name = $request->name;
                $row->designation = $request->designation;
                $row->phone = $request->phone;
                $row->salary = $request->salary;
                $row->rfid = $request->rfid;

                if ($row->save()) {
                    return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Employee Created Successfully');

                } else {
                    return RedirectHelper::backWithInput();
                }
            } else {
                return RedirectHelper::routeError($this->index_route, '<strong>Sorry !!!</strong>Data not found');

            }
        } catch (QueryException $e) {
            return $e;
            return RedirectHelper::backWithInputFromException();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public
    function destroy($id)
    {
        $this->checkOwnPermission('employees.delete');
        $deleteData = Employee::where('branch_id', auth()->user()->branch_id)
            ->find($id);

        if (!is_null($deleteData)) {
            if ($deleteData->delete()) {
                return response()->json(['status' => 200]);
            } else {
                return response()->json(['status' => 422]);
            }
        }
    }

    public function salary(Request $request, $id)
    {
        $this->checkOwnPermission('employees.edit');
        try {
//            $existingSalary = EmployeeSalary::where('employee_id', $id)
//                ->where('month', $request->month)
//                ->where('year', $request->year)
//                ->first();
//
//            if ($existingSalary) {
//                return RedirectHelper::backWithInput('<strong>Error: Salary record for this employee in '.$request->month.'-'.$request->year.' already exists.</strong>');
//            }
            \DB::beginTransaction();

            // Create EmployeeSalary record
            $row = new EmployeeSalary();
            $row->employee_id = $id;
            $row->month = $request->month;
            $row->year = $request->year;
            $row->salary = $request->salary;
            $row->note = $request->note;
            $row->refer_by = $request->refer_by;
            $row->payment_type = $request->payment_type;
//            $row->created_at = $request->created_at;

            if ($row->save()) {

                $cost = new Cost();
                $cost->branch_id = auth()->user()->branch_id;
                $cost->cost_category_id = Setting::get('salary_category');
                $cost->reason = 'Salary - '.$request->month.'-'.$request->year.' ('. (Employee::find($id))->name .')';
                $cost->amount = $request->salary;
                $cost->invoice_id =  null;
                $cost->refer_id =  null;
                $cost->account_details =  null;
                $cost->salary_id =  $row->id;
                $cost->account_type = null;
                $cost->payment_type = $request->payment_type;
                $cost->creation_date = $request->date ?? Carbon::now('Asia/Dhaka')->format('Y-m-d');
                $cost->save();

                \DB::commit();
                return RedirectHelper::routeSuccess($this->index_route, '<strong>Congratulations!!!</strong> Employee Created Successfully');
            } else {
                DB::rollBack();
                return RedirectHelper::backWithInput();
            }
        } catch (\Exception $e) {
//            dd($e);
            \DB::rollBack();
            return RedirectHelper::backWithInput();
        }


    }

    public function salaryDelete($id)
    {
        DB::transaction(function () use ($id) {
            $salary = EmployeeSalary::findOrFail($id);
            $salary->delete();

            Cost::where('salary_id',$id)->delete();
        });
            return RedirectHelper::back('<strong>Congratulations!!!</strong> Salary <Deleted></Deleted> Successfully');
    }
    public function getNetSalary($id)
    {
        $employee = \App\Models\Employee::findOrFail($id);
        return response()->json([
            'net_salary' => number_format($employee->net_salary, 2),
        ]);
    }

    public function getTotalCosts()
    {

        $employees = Employee::where('branch_id', auth()->user()->branch_id)
            ->select('id', 'name')
            ->withSum('costs', 'amount')
            ->get();


        return response()->json([
            'status' => 200,
            'data' => $employees->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'total_costs' => $employee->costs_sum_amount ?? 0
                ];
            })
        ]);
    }

    public function getAfterCost($id)
    {
        $employee = Employee::with('employeeSalaries')->findOrFail($id);

        $totalCost = $employee->employeeSalaries->sum('salary');
        $afterCost = $employee->salary - $totalCost;

        return response()->json([
            'after_cost' => $afterCost,
        ]);
    }

    // AJAX endpoint to check if RFID is unique
    public function checkRfidUnique(Request $request)
    {
        $rfid = $request->query('rfid');
        $ignoreId = $request->query('ignore_id');
        if (!$rfid) {
            return response()->json(['exists' => false]);
        }
        $query = Employee::where('rfid', $rfid);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }
        $exists = $query->exists();
        return response()->json(['exists' => $exists]);
    }

    /**
     * Display salary sheet with current and previous month details
     * @return \Illuminate\Http\Response
     */
    public function salarySheet(Request $request)
    {
        $this->checkOwnPermission('employees.index');

        // Get current and previous month/year
        $currentMonth = $request->get('month', Carbon::now()->format('F'));
        $currentYear = $request->get('year', Carbon::now()->format('Y'));
        $includeDeductions = $request->get('include_deductions', '1') == '1';

        // Calculate previous month
        $monthDate = Carbon::createFromFormat('F Y', "$currentMonth $currentYear");
        $previousMonth = $monthDate->copy()->subMonth()->format('F');
        $previousYear = $monthDate->copy()->subMonth()->format('Y');

        // Get all employees for the branch with their salaries and attendance
        $employees = Employee::where('branch_id', auth()->user()->branch_id)
            ->with([
                'employeeSalaries' => function ($query) use ($currentMonth, $currentYear, $previousMonth, $previousYear) {
                    $query->where(function ($q) use ($currentMonth, $currentYear, $previousMonth, $previousYear) {
                        $q->where(function ($q2) use ($currentMonth, $currentYear) {
                            $q2->where('month', $currentMonth)->where('year', $currentYear);
                        })->orWhere(function ($q2) use ($previousMonth, $previousYear) {
                            $q2->where('month', $previousMonth)->where('year', $previousYear);
                        });
                    });
                }
            ])
            ->orderBy('name', 'ASC')
            ->get();

        // Calculate totals - costs are tracked through salary payments
        $totalBaseSalary = $employees->sum('salary');
        $totalDeductions = 0;
        $totalHourlyDeductions = 0;
        $totalHours = 0;
        $totalDays = 0;

        // Get date range for the month
        $monthStart = $monthDate->copy()->startOfMonth()->toDateString();
        $monthEnd = $monthDate->copy()->endOfMonth()->toDateString();
        $daysInMonth = $monthDate->daysInMonth;
        $expectedDaysPerMonth = ($daysInMonth / 7) * 5; // 5 working days per week

        // Store attendance details for each employee
        $employeeAttendanceDetails = [];

        // Calculate deductions for each employee
        foreach ($employees as $employee) {
            // Calculate deductions from all salary payments made
            $employeeSalaryPayments = EmployeeSalary::where('employee_id', $employee->id)
                ->where('year', $currentYear)
                ->sum('salary');

            $totalDeductions += $employeeSalaryPayments;

            // Calculate hourly-based deductions if using hourly mode
            $attendanceMode = Setting::getByBranch($employee->branch_id, 'attendance_mode', 'standard');
            $attendanceDetails = $this->getAttendanceDetails($employee, $monthStart, $monthEnd, $currentMonth, $currentYear);

            $employeeAttendanceDetails[$employee->id] = $attendanceDetails;
            $totalHours += $attendanceDetails['totalHours'];
            $totalDays += $attendanceDetails['totalDays'];

            if ($attendanceMode === 'hourly') {
                $hourlyDeduction = $this->calculateHourlyDeduction($employee, $monthStart, $monthEnd, $currentMonth, $currentYear);
                $totalHourlyDeductions += $hourlyDeduction;
            }
        }

        $netTotal = $totalBaseSalary - $totalDeductions - $totalHourlyDeductions;

        $data = [
            'pageHeader' => $this->pageHeader,
            'employees' => $employees,
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
            'previousMonth' => $previousMonth,
            'previousYear' => $previousYear,
            'includeDeductions' => $includeDeductions,
            'totalBaseSalary' => $totalBaseSalary,
            'totalDeductions' => $totalDeductions,
            'totalHourlyDeductions' => $totalHourlyDeductions,
            'netTotal' => $netTotal,
            'monthStart' => $monthStart,
            'monthEnd' => $monthEnd,
            'daysInMonth' => $daysInMonth,
            'expectedDaysPerMonth' => $expectedDaysPerMonth,
            'totalHours' => $totalHours,
            'totalDays' => $totalDays,
            'employeeAttendanceDetails' => $employeeAttendanceDetails,
        ];

        return view('backend.pages.employees.salary-sheet', $data);
    }

    /**
     * Get attendance details for an employee in a month
     * @param Employee $employee
     * @param string $monthStart
     * @param string $monthEnd
     * @param string $currentMonth
     * @param string $currentYear
     * @return array
     */
    private function getAttendanceDetails($employee, $monthStart, $monthEnd, $currentMonth, $currentYear)
    {
        $monthDate = Carbon::createFromFormat('F Y', "$currentMonth $currentYear");
        $daysInMonth = $monthDate->daysInMonth;
        $expectedDaysPerMonth = ($daysInMonth / 7) * 5; // 5 working days per week

        $attendanceRecords = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->get();

        $recordsByDate = $attendanceRecords->groupBy('date');
        $totalHours = 0;
        $totalDays = $recordsByDate->count();

        $expectedHoursPerDay = 8;
        foreach ($recordsByDate as $date => $records) {
            $dayHours = 0;
            foreach ($records as $record) {
                if ($record->in_time && $record->out_time) {
                    $inTime = Carbon::parse($record->in_time);
                    $outTime = Carbon::parse($record->out_time);
                    $minutes = $outTime->diffInMinutes($inTime, false);
                    $dayHours += max(0, $minutes / 60);
                }
            }

            if ($dayHours === 0) {
                $dayHours = $expectedHoursPerDay;
            }

            $totalHours += $dayHours;
        }

        $totalHours = round($totalHours, 2);
        $missingDays = max(0, $expectedDaysPerMonth - $totalDays);
        $expectedTotalHours = $expectedDaysPerMonth * $expectedHoursPerDay;
        $missingHours = max(0, $expectedTotalHours - $totalHours);

        return [
            'totalHours' => $totalHours,
            'totalDays' => $totalDays,
            'expectedDays' => $expectedDaysPerMonth,
            'missingDays' => $missingDays,
            'expectedHours' => $expectedTotalHours,
            'missingHours' => $missingHours,
            'recordCount' => $attendanceRecords->count(),
        ];
    }

    /**
     * Calculate hourly-based salary deduction for an employee
     * @param Employee $employee
     * @param string $monthStart
     * @param string $monthEnd
     * @param string $currentMonth
     * @param string $currentYear
     * @return float
     */
    private function calculateHourlyDeduction($employee, $monthStart, $monthEnd, $currentMonth, $currentYear)
    {
        // Get total days in the month
        $daysInMonth = Carbon::createFromFormat('F Y', "$currentMonth $currentYear")->daysInMonth;

        // Get attendance records for the month
        $attendanceRecords = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->where('mode', 'hourly')
            ->get();

        // Count total hours attended
        $totalHoursAttended = 0;
        foreach ($attendanceRecords as $record) {
            if ($record->in_time && $record->out_time) {
                $inTime = Carbon::parse($record->in_time);
                $outTime = Carbon::parse($record->out_time);
                $minutes = $outTime->diffInMinutes($inTime, false);
                $hours = max(0, $minutes / 60);
                $totalHoursAttended += $hours;
            }
        }

        // Calculate expected hours (e.g., 8 hours per day, 5 working days per week)
        // Adjust this based on your company's working hours policy
        $expectedHoursPerDay = 8;
        $workingDaysPerWeek = 5;
        $expectedHoursPerMonth = ($daysInMonth / 7) * $workingDaysPerWeek * $expectedHoursPerDay;

        // Calculate missing hours
        $missingHours = max(0, $expectedHoursPerMonth - $totalHoursAttended);

        // Calculate hourly rate from monthly salary
        $monthlyHourlyRate = $employee->salary / $expectedHoursPerMonth;

        // Deduction for missing hours
        $hourlyDeduction = $missingHours * $monthlyHourlyRate;

        return $hourlyDeduction;
    }
}
