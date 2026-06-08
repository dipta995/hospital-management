@extends('backend.layouts.master')
@section('title')
    Salary Sheet - {{ $currentMonth }} {{ $currentYear }}
@endsection
@push('styles')
    <style>
        .salary-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .salary-filters {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .salary-table {
            border-collapse: collapse;
            width: 100%;
        }
        .salary-table thead {
            background-color: #f1f3f5;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        .salary-table th {
            padding: 12px;
            text-align: left;
            color: #333;
        }
        .salary-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #dee2e6;
        }
        .salary-table tbody tr:hover {
            background-color: #f9f9f9;
        }
        .salary-total {
            background-color: #e7f3ff;
            font-weight: 600;
            color: #0066cc;
        }
        .salary-amount {
            color: #27ae60;
            font-weight: 600;
        }
        .deduction-amount {
            color: #e74c3c;
            font-weight: 600;
        }
        .net-amount {
            color: #2980b9;
            font-weight: 700;
            font-size: 16px;
        }
        .toggle-deduction {
            cursor: pointer;
            user-select: none;
        }
        .badge-paid {
            background-color: #27ae60;
        }
        .badge-unpaid {
            background-color: #e74c3c;
        }
        .summary-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .summary-item:last-child {
            border-bottom: none;
        }
        .summary-label {
            color: #666;
            font-weight: 500;
        }
        .summary-value {
            color: #333;
            font-weight: 700;
            font-size: 16px;
        }
    </style>
@endpush

@section('admin-content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Header -->
                    <div class="salary-header">
                        <h2 class="mb-2">📊 Salary Sheet</h2>
                        <p class="mb-0">{{ $currentMonth }} {{ $currentYear }}</p>
                    </div>

                    <!-- Filters -->
                    <div class="card">
                        <div class="card-body salary-filters">
                            <form method="get" class="row g-3">
                                <div class="col-md-3">
                                    <label for="month" class="form-label">Month</label>
                                    <select class="form-select" name="month" id="month" required>
                                        <option value="" disabled>Select Month</option>
                                        @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                            <option value="{{ $month }}" {{ $month == $currentMonth ? 'selected' : '' }}>
                                                {{ $month }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="year" class="form-label">Year</label>
                                    <select class="form-select" name="year" id="year" required>
                                        @for($y = date('Y')-2; $y <= date('Y')+1; $y++)
                                            <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>
                                                {{ $y }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="include_deductions" class="form-label">Options</label>
                                    <select class="form-select" name="include_deductions" id="include_deductions">
                                        <option value="1" {{ request('include_deductions', '1') == '1' ? 'selected' : '' }}>
                                            Include Deductions
                                        </option>
                                        <option value="0" {{ request('include_deductions') == '0' ? 'selected' : '' }}>
                                            Exclude Deductions
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex gap-2 align-items-end">
                                    <button type="submit" class="btn btn-info flex-fill">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                    <button type="button" class="btn btn-success" onclick="window.print()" title="Print">
                                        <i class="fas fa-print"></i>
                                    </button>
                                    <a href="{{ route('admin.employees.index', ['month' => $currentMonth, 'year' => $currentYear]) }}"
                                       class="btn btn-primary" title="Export PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if(!empty($hrSchemaInstalled))
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <div class="summary-card">
                                <div class="summary-item">
                                    <span class="summary-label">Total Off Days</span>
                                    <span class="summary-value text-secondary">{{ $totalOffDays }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-card">
                                <div class="summary-item">
                                    <span class="summary-label">Total Leave Days</span>
                                    <span class="summary-value text-info">{{ $totalLeaveDays }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-card">
                                <div class="summary-item">
                                    <span class="summary-label">Total Absences</span>
                                    <span class="summary-value deduction-amount">{{ $totalAbsenceDays }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-card">
                                <div class="summary-item">
                                    <span class="summary-label">Absence Deductions</span>
                                    <span class="summary-value deduction-amount">৳ {{ number_format($totalAbsenceDeductions, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Summary Cards -->
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="summary-card">
                                <div class="summary-item">
                                    <span class="summary-label">Total Employees</span>
                                    <span class="summary-value text-primary">{{ $employees->count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-card">
                                <div class="summary-item">
                                    <span class="summary-label">Total Base Salary</span>
                                    <span class="summary-value salary-amount">
                                        ৳ {{ number_format($totalBaseSalary, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-card">
                                <div class="summary-item">
                                    <span class="summary-label">Total Deductions</span>
                                    <span class="summary-value deduction-amount">
                                        ৳ {{ number_format($totalDeductions + $totalHourlyDeductions + ($totalAbsenceDeductions ?? 0), 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-card">
                                <div class="summary-item">
                                    <span class="summary-label">Net Total</span>
                                    <span class="summary-value net-amount">
                                        ৳ {{ number_format($netTotal, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Salary Table -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Detailed Salary Breakdown</h5>

                            @if($employees->count() > 0)
                                <div class="table-responsive">
                                    <table class="table salary-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 3%">#</th>
                                                <th style="width: 15%">Employee Name</th>
                                                <th style="width: 12%">Designation</th>
                                                <th style="width: 8%">Present</th>
                                                @if(!empty($hrSchemaInstalled))
                                                    <th style="width: 6%">Off</th>
                                                    <th style="width: 6%">Leave</th>
                                                    <th style="width: 6%">Absent</th>
                                                    <th style="width: 6%">Rate</th>
                                                @endif
                                                <th style="width: 8%">Hours</th>
                                                <th style="width: 9%">Base Salary</th>
                                                @if($includeDeductions)
                                                    <th style="width: 12%">Deductions</th>
                                                    <th style="width: 12%">Net Salary</th>
                                                @else
                                                    <th style="width: 12%">Payable</th>
                                                @endif
                                                <th style="width: 8%">Status</th>
                                                <th style="width: 8%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($employees as $employee)
                                                @php
                                                    $baseSalary = $employee->salary;
                                                    $currentMonthPaid = $employee->employeeSalaries
                                                        ->where('month', $currentMonth)
                                                        ->where('year', $currentYear)
                                                        ->first();
                                                    $lastMonthPaid = $employee->employeeSalaries
                                                        ->where('month', $previousMonth)
                                                        ->where('year', $currentYear)
                                                        ->first();

                                                    // Calculate total deductions from salary payments
                                                    $salaryPaymentDeductions = $employee->employeeSalaries
                                                        ->where('year', $currentYear)
                                                        ->sum('salary');

                                                    $attendanceDetails = $employeeAttendanceDetails[$employee->id] ?? [
                                                        'totalDays' => 0,
                                                        'totalHours' => 0,
                                                        'expectedDays' => 0,
                                                        'missingDays' => 0,
                                                        'expectedHours' => 0,
                                                        'missingHours' => 0,
                                                        'recordCount' => 0,
                                                        'weeklyOffCount' => 0,
                                                        'leaveCount' => 0,
                                                        'absenceCount' => 0,
                                                        'attendanceRate' => 0,
                                                        'absenceDeduction' => 0,
                                                        'hourlyDeduction' => 0,
                                                    ];

                                                    $hourlyDeduction = $attendanceDetails['hourlyDeduction'] ?? 0;
                                                    $absenceDeduction = ($includeDeductions && !empty($hrSchemaInstalled))
                                                        ? ($attendanceDetails['absenceDeduction'] ?? 0)
                                                        : 0;

                                                    $totalDeductionsForEmployee = $salaryPaymentDeductions + $hourlyDeduction + $absenceDeduction;
                                                    $netSalary = $baseSalary - $totalDeductionsForEmployee;
                                                    $paidAmount = $currentMonthPaid ? $currentMonthPaid->salary : 0;
                                                    $isPaid = $currentMonthPaid ? true : false;
                                                @endphp
                                                <tr>
                                                    <td>{{ $loop->index + 1 }}</td>
                                                    <td>
                                                        <strong>{{ $employee->name }}</strong>
                                                    </td>
                                                    <td>{{ $employee->designation ?? 'N/A' }}</td>
                                                    <td>{{ $attendanceDetails['totalDays'] ?? 0 }}</td>
                                                    @if(!empty($hrSchemaInstalled))
                                                        <td>{{ $attendanceDetails['weeklyOffCount'] ?? 0 }}</td>
                                                        <td>{{ $attendanceDetails['leaveCount'] ?? 0 }}</td>
                                                        <td class="deduction-amount">{{ $attendanceDetails['absenceCount'] ?? 0 }}</td>
                                                        <td>{{ $attendanceDetails['attendanceRate'] ?? 0 }}%</td>
                                                    @endif
                                                    <td>{{ number_format($attendanceDetails['totalHours'] ?? 0, 2) }}</td>
                                                    <td class="salary-amount">৳ {{ number_format($baseSalary, 2) }}</td>

                                                    @if($includeDeductions)
                                                        <td class="deduction-amount">
                                                            {{ $totalDeductionsForEmployee > 0 ? '- ৳ ' . number_format($totalDeductionsForEmployee, 2) : '৳ 0' }}
                                                        </td>
                                                        <td class="net-amount">
                                                            ৳ {{ number_format(max(0, $netSalary), 2) }}
                                                        </td>
                                                    @else
                                                        <td class="salary-amount">
                                                            ৳ {{ number_format($baseSalary, 2) }}
                                                        </td>
                                                    @endif

                                                    <td>
                                                        @if($isPaid)
                                                            <span class="badge badge-paid">
                                                                <i class="fas fa-check-circle"></i> Paid
                                                            </span>
                                                        @else
                                                            <span class="badge badge-unpaid">
                                                                <i class="fas fa-times-circle"></i> Unpaid
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.employees.show', $employee->id) }}"
                                                           class="btn btn-sm btn-info" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.employees.edit', $employee->id) }}"
                                                           class="btn btn-sm btn-warning" title="Edit">
                                                            <i class="fas fa-pencil"></i>
                                                        </a>
                                                        @if(!empty($hrSchemaInstalled))
                                                            <a href="{{ route('admin.employees.leave-days.index', ['employee' => $employee->id, 'month' => $currentMonth, 'year' => $currentYear]) }}"
                                                               class="btn btn-sm btn-secondary" title="Leave & Off Days">
                                                                <i class="fas fa-calendar-alt"></i>
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted py-4">
                                                        <i class="fas fa-inbox" style="font-size: 30px; opacity: 0.3;"></i>
                                                        <p class="mt-2">No employees found</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Footer Summary -->
                                <div class="row mt-4 border-top pt-3">
                                    <div class="col-md-6">
                                        <h6 class="text-muted">Month: <strong>{{ $currentMonth }} {{ $currentYear }}</strong></h6>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <p class="mb-1">
                                            <strong>Total Base Salary:</strong>
                                            <span class="salary-amount">৳ {{ number_format($totalBaseSalary, 2) }}</span>
                                        </p>
                                        @if($includeDeductions)
                                            <p class="mb-1">
                                                <strong>Manual Deductions:</strong>
                                                <span class="deduction-amount">- ৳ {{ number_format($totalDeductions, 2) }}</span>
                                            </p>
                                            @if($totalHourlyDeductions > 0)
                                                <p class="mb-1">
                                                    <strong>Hourly Deductions:</strong>
                                                    <span class="deduction-amount">- ৳ {{ number_format($totalHourlyDeductions, 2) }}</span>
                                                </p>
                                            @endif
                                            @if(!empty($hrSchemaInstalled) && $totalAbsenceDeductions > 0)
                                                <p class="mb-1">
                                                    <strong>Absence Deductions:</strong>
                                                    <span class="deduction-amount">- ৳ {{ number_format($totalAbsenceDeductions, 2) }}</span>
                                                </p>
                                            @endif
                                            <p class="mb-1">
                                                <strong>Total Deductions:</strong>
                                                <span class="deduction-amount">- ৳ {{ number_format($totalDeductions + $totalHourlyDeductions + ($totalAbsenceDeductions ?? 0), 2) }}</span>
                                            </p>
                                        @endif
                                        <p class="mb-0">
                                            <strong>Net Payable:</strong>
                                            <span class="net-amount">৳ {{ number_format($netTotal, 2) }}</span>
                                        </p>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle"></i> No salary data available for {{ $currentMonth }} {{ $currentYear }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Print functionality
            function printSalarySheet() {
                window.print();
            }

            // Filter form submission
            document.getElementById('month').addEventListener('change', function() {
                document.querySelector('form').submit();
            });
            document.getElementById('year').addEventListener('change', function() {
                document.querySelector('form').submit();
            });
            document.getElementById('include_deductions').addEventListener('change', function() {
                document.querySelector('form').submit();
            });
        </script>
    @endpush
@endsection
