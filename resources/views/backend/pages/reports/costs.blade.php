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
                            <h2 class="bg-info">By Individual Category</h2>
                            <form action="{{ route('admin.costs.report-category-pdf-id') }}" method="GET" class="mb-4">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="start_date">Start Date:</label>
                                        <input type="date" name="start_date" id="start_date" class="form-control"
                                               value="{{ request('start_date') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="end_date">End Date:</label>
                                        <input type="date" name="end_date" id="end_date" class="form-control"
                                               value="{{ request('end_date') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="end_date">Category:</label>
                                        <select class="form-control" name="cost_category_id" id="">
                                            @foreach(\App\Models\CostCategory::where('branch_id',auth()->user()->branch_id)->get() as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                                <option value="">PC</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ route('admin.costs.index') }}"
                                           class="btn btn-secondary ms-2">Reset</a>
                                    </div>
                                </div>
                            </form>
                            <h2 class="bg-info">By Categories</h2>
                            <form action="{{ route('admin.costs.report-category-pdf') }}" method="GET" class="mb-4">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="start_date">Start Date:</label>
                                        <input type="date" name="start_date" id="start_date" class="form-control"
                                               value="{{ request('start_date') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="end_date">End Date:</label>
                                        <input type="date" name="end_date" id="end_date" class="form-control"
                                               value="{{ request('end_date') }}">
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ route('admin.costs.index') }}"
                                           class="btn btn-secondary ms-2">Reset</a>
                                    </div>
                                </div>
                            </form>

                            <h2 class="bg-info">By Date</h2>
                            <form action="{{ route('admin.costs.report-pdf') }}" method="GET" class="mb-4">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="start_date">Start Date:</label>
                                        <input type="date" name="start_date" id="start_date" class="form-control"
                                               value="{{ request('start_date') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="end_date">End Date:</label>
                                        <input type="date" name="end_date" id="end_date" class="form-control"
                                               value="{{ request('end_date') }}">
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ route('admin.costs.index') }}"
                                           class="btn btn-secondary ms-2">Reset</a>
                                    </div>
                                </div>
                            </form>
                            <p>Total: {{ $totalAmount }}</p>
                            <p class="card-description">
                                @include('backend.layouts.partials.message')
                            </p>
                            <div class="table-responsive">

                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Reason</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($datas as $item)
                                        <tr id="table-data{{ $item->id }}">
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $item->reeferBy->name ?? ($item->category->name ?? 'N/A') }}</td>
                                            <td>{{ $item->reason ?? 'N/A' }}</td>
                                            <td>{{ $item->amount }}</td>
                                            <td>{{ $item->creation_date }}</td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td>No record Found</td>
                                        </tr>
                                    @endforelse

                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-end">
                                    {!! $datas->appends(request()->query())->links() !!}
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
