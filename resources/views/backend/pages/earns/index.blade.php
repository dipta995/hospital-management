@extends('backend.layouts.master')
@section('title')
    List of {{ $pageHeader['title'] }}'s
@endsection

@push('styles')
<!-- Add any additional styles if needed -->
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
                            <form method="get">
                                <div class="form-group d-inline-block mr-2">
                                    <x-default.label required="true" for="month">Month</x-default.label>
                                    <select class="form-control form-control-sm" name="month" id="month">
                                        <option value="">Choose Month</option>
                                        <option value="1" @if(request('month') == '1') selected @endif>January</option>
                                        <option value="2" @if(request('month') == '2') selected @endif>February</option>
                                        <option value="3" @if(request('month') == '3') selected @endif>March</option>
                                        <option value="4" @if(request('month') == '4') selected @endif>April</option>
                                        <option value="5" @if(request('month') == '5') selected @endif>May</option>
                                        <option value="6" @if(request('month') == '6') selected @endif>June</option>
                                        <option value="7" @if(request('month') == '7') selected @endif>July</option>
                                        <option value="8" @if(request('month') == '8') selected @endif>August</option>
                                        <option value="9" @if(request('month') == '9') selected @endif>September</option>
                                        <option value="10" @if(request('month') == '10') selected @endif>October</option>
                                        <option value="11" @if(request('month') == '11') selected @endif>November</option>
                                        <option value="12" @if(request('month') == '12') selected @endif>December</option>
                                    </select>
                                </div>

                                <div class="form-group d-inline-block mr-2">
                                    <x-default.label required="true" for="type">Type</x-default.label>
                                    <select class="form-control form-control-sm" name="type" id="type">
                                        <option value="">All Types</option>
                                        <option value="Loan" @if(request('type') == 'Loan') selected @endif>Loan</option>
                                        <option value="Deposit" @if(request('type') == 'Deposit') selected @endif>Deposit</option>
                                        <option value="Advance From Shareholder" @if(request('type') == 'Advance From Shareholder') selected @endif>Advance From Shareholder</option>
                                        <option value="Withdrew" @if(request('type') == 'Withdrew') selected @endif>Withdrew</option>
                                        <option value="Other" @if(request('type') == 'Other') selected @endif>Other</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary btn-sm">Search</button>
                            </form>

                            <div class="table-responsive mt-3">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                            <th>Note</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($datas as $item)
                                        <tr id="table-data{{ $item->id }}">
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->type }}</td>
                                            <td> {{ $item->amount }}  Taka </td>
                                            <td>{{ \Carbon\Carbon::parse($item->date)->format('Y-m-d') }}</td> <!-- Display formatted date -->
                                            <td>{{ $item->note }}</td>
                                            <td>
                                                <a href="{{ route($pageHeader['edit_route'],$item->id) }}" class="badge bg-info"><i class="fas fa-pen"></i></a>
                                                <a class="badge bg-danger" href="javascript:void(0)" onclick="dataDelete({{ $item->id }},'{{ $pageHeader['base_url'] }}')"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7">No records found <a href="{{ route($pageHeader['create_route']) }}" class="btn btn-info">Create</a></td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>

                                <div class="d-flex justify-content-end">
                                    {!! $datas->links() !!}
                                </div>

                                <!-- Display Total Earnings -->
                                <div class="mt-3">
                                    <strong>Total Earnings: Taka </strong> {{ $totalEarnings }}
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
<script>
    function dataDelete(id, baseUrl) {
        if (confirm("Are you sure you want to delete this record?")) {
            $.ajax({
                url: baseUrl + '/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.status === 200) {
                        $('#table-data' + id).remove();
                        alert('Deleted successfully!');
                    } else {
                        alert('Delete failed. Please try again.');
                    }
                },
                error: function () {
                    alert('Something went wrong!');
                }
            });
        }
    }
</script>
@endpush
