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
                            <h4 class="card-title">{{ $pageHeader['title'] }}'s List</h4>
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
                                        <tr id="table-data{{ $item->id }}">
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $item->name }}</td>
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
                                                <a class="badge bg-danger" href="javascript:void(0)"
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
@endsection

@push('scripts')

@endpush
