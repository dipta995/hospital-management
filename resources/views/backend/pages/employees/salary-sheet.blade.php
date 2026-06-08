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

        .print-only {
            display: none;
        }

        .salary-table-scroll-hint {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            margin-bottom: 8px;
            border-radius: 8px;
            background: #ecfeff;
            border: 1px solid #99f6e4;
            color: #0f766e;
            font-size: 0.88rem;
            font-weight: 600;
        }

        .salary-table-scroll-top {
            overflow-x: auto;
            overflow-y: hidden;
            margin-bottom: 6px;
            border: 1px solid #dbeafe;
            border-radius: 8px;
            background: #f8fafc;
            height: 18px;
        }

        .salary-table-scroll-top::-webkit-scrollbar {
            height: 14px;
        }

        .salary-table-scroll-top::-webkit-scrollbar-thumb {
            background: #0f766e;
            border-radius: 999px;
        }

        .salary-table-scroll-top::-webkit-scrollbar-track {
            background: #e2e8f0;
            border-radius: 999px;
        }

        #salary-table-scroll-top-inner {
            height: 1px;
        }

        .salary-table-wrapper .table-responsive {
            overflow-x: auto;
        }

        .salary-table-wrapper .table-responsive::-webkit-scrollbar {
            height: 14px;
        }

        .salary-table-wrapper .table-responsive::-webkit-scrollbar-thumb {
            background: #64748b;
            border-radius: 999px;
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 8mm;
            }

            body,
            html {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .topbar,
            .main-nav,
            .footer,
            footer,
            .offcanvas,
            .no-print,
            .print-hide,
            .salary-table-scroll-top,
            .salary-table-scroll-hint {
                display: none !important;
            }

            html,
            body,
            .wrapper {
                overflow: visible !important;
            }

            .wrapper,
            .page-content,
            .main-panel,
            .content-wrapper,
            .row,
            .col-lg-12,
            #salary-sheet-print {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                box-shadow: none !important;
            }

            .print-only {
                display: block !important;
            }

            .salary-header {
                background: #0f766e !important;
                color: #fff !important;
                border-radius: 0 !important;
                padding: 12px 14px !important;
                margin-bottom: 10px !important;
            }

            .summary-card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
                padding: 10px !important;
                margin-bottom: 8px !important;
                break-inside: avoid;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }

            .card-body {
                padding: 0 !important;
            }

            .table-responsive {
                overflow: visible !important;
                width: 100% !important;
            }

            .salary-table {
                width: 100% !important;
                table-layout: fixed !important;
                font-size: 9px !important;
                border-collapse: collapse !important;
            }

            .salary-table th,
            .salary-table td {
                padding: 4px 3px !important;
                border: 1px solid #ccc !important;
                word-wrap: break-word !important;
                overflow-wrap: anywhere !important;
                white-space: normal !important;
                vertical-align: top !important;
            }

            .salary-table thead {
                background: #f1f3f5 !important;
            }

            .salary-table tbody tr {
                break-inside: avoid;
            }

            .badge-paid,
            .badge-unpaid {
                background: transparent !important;
                color: #000 !important;
                border: 1px solid #999 !important;
                padding: 1px 4px !important;
                font-size: 8px !important;
            }

            .salary-amount,
            .deduction-amount,
            .net-amount {
                color: #000 !important;
                font-size: 9px !important;
            }

            .print-summary-row {
                display: flex !important;
                flex-wrap: wrap !important;
                gap: 8px !important;
                margin-bottom: 10px !important;
            }

            .print-summary-row .summary-card {
                flex: 1 1 22% !important;
                min-width: 120px !important;
            }
        }
    </style>
@endpush

@section('admin-content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12" id="salary-sheet-print">
                    <div class="print-only mb-2">
                        <h3 class="mb-1">{{ \App\Models\Setting::get('company_name', 'Hospital Management Software') }}</h3>
                        <p class="mb-0">Employee Salary Sheet — {{ $currentMonth }} {{ $currentYear }}</p>
                    </div>

                    <!-- Header -->
                    <div class="salary-header">
                        <h2 class="mb-2">Salary Sheet</h2>
                        <p class="mb-0">{{ $currentMonth }} {{ $currentYear }}</p>
                    </div>

                    <!-- Filters -->
                    <div class="card no-print">
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

                    @if(!empty($canSummarizeAttendance))
                    <div class="row mt-3 print-summary-row">
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
                    <div class="row mt-4 print-summary-row">
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
                                <div class="salary-table-scroll-hint no-print">
                                    <i class="fas fa-arrows-left-right"></i>
                                    <span>Table is wide — use the scrollbar above or below to view all columns.</span>
                                </div>

                                <div class="salary-table-wrapper">
                                    <div class="salary-table-scroll-top no-print" id="salary-table-scroll-top">
                                        <div id="salary-table-scroll-top-inner"></div>
                                    </div>

                                    <div class="table-responsive" id="salary-table-scroll-main">
                                    <table class="table salary-table" id="salary-sheet-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 3%">#</th>
                                                <th style="width: 15%">Employee Name</th>
                                                <th style="width: 12%">Designation</th>
                                                <th style="width: 8%">Present</th>
                                                @if(!empty($canSummarizeAttendance))
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
                                                <th style="width: 8%" class="print-hide">Action</th>
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
                                                    $absenceDeduction = ($includeDeductions && !empty($canSummarizeAttendance))
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
                                                    @if(!empty($canSummarizeAttendance))
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
                                                    <td class="print-hide">
                                                        <a href="{{ route('admin.employees.show', $employee->id) }}"
                                                           class="btn btn-sm btn-info" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.employees.edit', $employee->id) }}"
                                                           class="btn btn-sm btn-warning" title="Edit">
                                                            <i class="fas fa-pencil"></i>
                                                        </a>
                                                        @if(!empty($canSummarizeAttendance))
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

            (function () {
                const topScroller = document.getElementById('salary-table-scroll-top');
                const topInner = document.getElementById('salary-table-scroll-top-inner');
                const mainScroller = document.getElementById('salary-table-scroll-main');
                const table = document.getElementById('salary-sheet-table');

                if (!topScroller || !topInner || !mainScroller || !table) {
                    return;
                }

                let syncing = false;

                function syncWidths() {
                    topInner.style.width = table.scrollWidth + 'px';
                }

                function syncFromTop() {
                    if (syncing) return;
                    syncing = true;
                    mainScroller.scrollLeft = topScroller.scrollLeft;
                    syncing = false;
                }

                function syncFromMain() {
                    if (syncing) return;
                    syncing = true;
                    topScroller.scrollLeft = mainScroller.scrollLeft;
                    syncing = false;
                }

                topScroller.addEventListener('scroll', syncFromTop);
                mainScroller.addEventListener('scroll', syncFromMain);
                window.addEventListener('resize', syncWidths);
                window.addEventListener('load', syncWidths);

                syncWidths();

                if (typeof ResizeObserver !== 'undefined') {
                    const observer = new ResizeObserver(syncWidths);
                    observer.observe(table);
                }
            })();
        </script>
    @endpush
@endsection
