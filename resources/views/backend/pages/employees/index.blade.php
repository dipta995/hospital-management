@extends('backend.layouts.master')
@section('title')
    List of {{ $pageHeader['title'] }}'s
@endsection
@push('styles')

@endpush
@section('admin-content')
    <!-- partial -->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">

                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">{{ $pageHeader['title'] }}'s List
                                <a href="{{ route('admin.attendance.index') }}" class="btn btn-primary btn-sm float-end">Attendance</a>
                                <a href="{{ route('admin.employees.salary-sheet') }}" class="btn btn-success btn-sm float-end me-2">
                                    <i class="fas fa-file-invoice-dollar"></i> Salary Sheet
                                </a>
                            </h4>
                            <p class="card-description">
                            @include('backend.layouts.partials.message')
                            </p>

                            <form method="get" class="row">
                                <div class="col-md-3">
                                    <label for="month" class="form-label">Month(Choose Salary Month)</label>
                                    <select class="form-select" name="month"
                                            id="month" required>
                                        <option value="" disabled selected>Select what was the month</option>
                                        @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                            <option value="{{ $month }}"
                                                    @if($month == ucfirst(Carbon\Carbon::now()->subMonth()->format('F'))) selected @endif>{{ $month }}</option>
                                        @endforeach

                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="year" class="form-label">Choose Year</label>
                                    <select class="form-select" name="year"
                                            id="year" required>
                                        <option value="" disabled selected>Select what was the year</option>
                                        <option value="{{ date('Y')-1 }}">{{ date('Y')-1 }}</option>
                                        <option selected
                                                value="{{ date('Y') }}">{{ date('Y') }}</option>
                                        <option value="{{ date('Y')+1 }}">{{ date('Y')+1 }}</option>

                                    </select>
                                </div>
                                <div class="col-md-3 mt-4">
                                    <button type="submit" class="btn btn-info">Export Sheet</button>
                                </div>
                            </form>

                            <ul class="nav nav-pills emp-tabs mt-3 mb-2">
                                @foreach(['current' => 'Current Staff', 'resigned' => 'Resigned', 'all' => 'All'] as $tabKey => $tabLabel)
                                    <li class="nav-item">
                                        <a class="nav-link {{ $tab === $tabKey ? 'active' : '' }}"
                                           href="{{ route($pageHeader['index_route'], ['tab' => $tabKey]) }}">
                                            {{ $tabLabel }} <span class="badge {{ $tab === $tabKey ? 'bg-light text-dark' : 'bg-secondary' }}">{{ $tabCounts[$tabKey] }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            @if(!$resignInstalled)
                                <div class="alert alert-warning py-2 small mb-2">
                                    Resign option is not active yet. Install <strong>Employee Resignation</strong> from Dashboard → System Updates.
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Designation</th>
                                        <th>Salary</th>
                                        <th>Amount Costs</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($datas as $item)
                                        <tr id="table-data{{ $item->id }}" class="{{ $item->isResigned() ? 'emp-resigned' : '' }}">
                                            <td>{{ $datas->firstItem() + $loop->index }}</td>
                                            <td>
                                                {{ $item->name }}
                                                @if($item->isResigned())
                                                    @php $leavingLater = $item->resigned_at && $item->resigned_at->isAfter(now('Asia/Dhaka')->startOfDay()); @endphp
                                                    <br>
                                                    <span class="badge {{ $leavingLater ? 'bg-warning text-dark' : 'bg-secondary' }}"
                                                          @if($item->resign_reason) title="{{ $item->resign_reason }}" data-bs-toggle="tooltip" @endif>
                                                        {{ $leavingLater ? 'Leaving' : 'Resigned' }}{{ $item->resigned_at ? ' · ' . $item->resigned_at->format('d M Y') : '' }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ $item->phone }}</td>
                                            <td>{{ $item->designation }}</td>
                                            {{-- <td>{{ $item->salary }}
                                                <span class="badge bg-{{ $item->salary_paid == true ? 'info' : 'danger' }}">
                                                    {{ $item->salary_paid == true ? 'Paid' : 'Due' }}
                                                </span>
                                            </td>
                                            <td>{{ $item->created_at }}</td> --}}


                                            {{-- <td>
                                                {{ $item->salary }}
                                                <span class="badge bg-{{ $item->salary_paid == true ? 'info' : 'danger' }}">
                                                    {{ $item->salary_paid == true ? 'Paid' : 'Due' }}
                                                </span>
                                            </td> --}}
                                            {{-- <td>
                                                <strong>Base:</strong> {{ number_format($item->salary, 2) }} <br>
                                                    <strong>After Costs:</strong>
                                                <span class="text-danger net-salary" id="employee-{{ $item->id }}">
                                                    {{ number_format($item->net_salary, 2) }}

                                                </span>


                                            </td> --}}

                                            @php
                                                $totalCost = $item->employeeSalaries->sum('salary'); // total cost amount
                                                $afterCost = $item->salary - $totalCost; // final amount
                                            @endphp

                                            <td>
                                                <strong>Base:</strong> {{ number_format($item->salary, 2) }} <br>
                                                <strong>Amount Costs:</strong> {{ number_format($totalCost, 2) }} <br>
                                                <strong>After Costs:</strong>
                                                <span class="text-danger net-salary" id="employee-{{ $item->id }}">
                                                    {{ number_format($afterCost, 2) }}
                                                </span>
                                            </td>










                                            {{-- <td>{{ number_format($item->total_costs, 2) }}</td> --}}

                                            {{-- <td id="employee-total-{{ $item->id }}">{{ number_format($item->total_costs, 2) }}</td> --}}

                                            <td>
                                                @foreach($item->employeeSalaries as $salary)
                                                    {{ $salary->salary }} TK <br>
                                                @endforeach
                                            </td>



                                            <td>{{ $item->created_at->format('Y-m-d') }}</td>


                                                          <td>
                                                                <a href="{{ route($pageHeader['edit_route'],$item->id) }}"
                                                                    class="badge bg-success"><i class="fas fa-pencil"></i></a>
                                                                <a href="{{ route('admin.employees.show',$item->id) }}"
                                                                    class="badge bg-info"><i class="fas fa-eye"></i></a>
                                                                <a href="{{ route('admin.attendance.index', ['employee_id' => $item->id]) }}" class="badge bg-warning" title="Attendance Record"><i class="fas fa-calendar-check"></i></a>
                                                                @if(!empty($hrSchemaInstalled))
                                                                    <a href="{{ route('admin.employees.leave-days.index', $item->id) }}" class="badge bg-secondary" title="Leave & Off Days"><i class="fas fa-calendar-alt"></i></a>
                                                                @endif
                                                                @if($item->isResigned())
                                                                    <form action="{{ route('admin.employees.rejoin', $item->id) }}" method="POST" class="d-inline"
                                                                          onsubmit="return confirm('Make {{ e(addslashes($item->name)) }} active again?')">
                                                                        @csrf
                                                                        <button type="submit" class="badge bg-primary border-0" title="Rejoin"><i class="fas fa-user-check"></i></button>
                                                                    </form>
                                                                @elseif($resignInstalled)
                                                                    <a href="javascript:void(0)" class="badge bg-dark" title="Resign"
                                                                       data-bs-toggle="modal" data-bs-target="#resignModal"
                                                                       data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                                                       data-url="{{ route('admin.employees.resign', $item->id) }}">
                                                                        <i class="fas fa-user-slash"></i>
                                                                    </a>
                                                                @endif
                                                                <a class="badge bg-danger" href="javascript:void(0)" title="Delete"
                                                                    onclick="dataDelete({{ $item->id }},'{{ $pageHeader['base_url'] }}')"><i class="fas fa-trash"></i></a>
                                                          </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td>No record Found <a href="{{ route($pageHeader['create_route']) }}"
                                                                   class="btn btn-info">Create</a></td>
                                        </tr>
                                    @endforelse

                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-end">
                                    {!! $datas->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
    <!-- main-panel ends -->

    @if($resignInstalled)
        <div class="modal fade" id="resignModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="" class="modal-content" id="resignForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Resign: <span id="resignName"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" for="resigned_at">Last working day <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="resigned_at" id="resigned_at"
                                   value="{{ now('Asia/Dhaka')->toDateString() }}" required>
                            <small class="text-muted">Attendance and salary sheets stop counting this employee after this date. A future date works as a notice period.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="resign_reason">Reason</label>
                            <textarea class="form-control" name="resign_reason" id="resign_reason" rows="2" maxlength="500"
                                      placeholder="e.g. Personal reason, better job, terminated"></textarea>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="release_rfid" value="1" id="release_rfid">
                            <label class="form-check-label" for="release_rfid">
                                Release RFID card (so it can be given to a new employee)
                            </label>
                        </div>
                        <div class="alert alert-info small mt-3 mb-0">
                            Old salary, attendance and leave records are kept. You can Rejoin the employee later.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark"><i class="fas fa-user-slash"></i> Mark as Resigned</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection

@push('styles')
    <style>
        .emp-tabs .nav-link { padding: .35rem .9rem; font-size: .875rem; }
        tr.emp-resigned td { color: #64748b; }
    </style>
@endpush

@push('scripts')
    <script>
        document.getElementById('resignModal')?.addEventListener('show.bs.modal', function (event) {
            var trigger = event.relatedTarget;
            document.getElementById('resignForm').action = trigger.getAttribute('data-url');
            document.getElementById('resignName').textContent = trigger.getAttribute('data-name');
            document.getElementById('resign_reason').value = '';
            document.getElementById('release_rfid').checked = false;
        });
    </script>
@endpush
