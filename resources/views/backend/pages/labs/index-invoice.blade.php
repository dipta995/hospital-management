@extends('backend.layouts.master')
@section('title')
    List of {{ $pageHeader['title'] }}
    @php
        $userGuard = Auth::guard('admin')->user();
    @endphp
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
                            <form action="" method="GET" class="mb-4">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="start_date">Start Date:</label>
                                        <input type="date" name="start_date" id="start_date" class="form-control"
                                               value="{{ request('start_date') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="end_date">End Date:</label>
                                        <input type="date" name="end_date" id="end_date" class="form-control"
                                               value="{{ request('end_date') }}">
                                    </div>


                                    <div class="col-md-2">
                                        <label for="end_date">Invoice:</label>
                                        <input type="text" name="invoice_number" id="invoice_number"
                                               class="form-control"
                                               value="{{ request('invoice_number') }}">
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary ms-2">Reset</a>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th># [Patient Id] ~ Name</th>
                                        <th>Tests</th>
                                        <th>LAB</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($datas as $item)
                                        <tr id="table-data{{ $item->id }}">
                                            <td>{{ $loop->index + 1 }}. {{ $item->invoice_number }} [{{ $item->patient_no }}]
                                                <hr>
                                                {{ $item->patient_name ?? 'NA'}}</td>
                                            <td>
                                                @foreach($item->tests as $product)
                                                    @php
                                                        $status = $product->status;
                                                        $complete = \App\Models\InvoiceList::$statusArray[2];
                                                        $processing = \App\Models\InvoiceList::$statusArray[1];
                                                        $badgeClass = 'bg-dark';
                                                        if ($status === $complete) {
                                                            $badgeClass = 'bg-success';
                                                        } elseif ($status === $processing) {
                                                            $badgeClass = 'bg-warning';
                                                        }
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}">{{ $product->product->name }} </span>
                                                @endforeach
                                            </td>



                                            <td>
                                                <strong
                                                    class="badge  {{ $item->isFullyProcessed() ? 'bg-success' : 'bg-warning' }}">{{ $item->isFullyProcessed() ? 'Complete' : 'Pending' }}</strong>
                                            </td>

                                            <td>
                                                <a target="_blank"
                                                   href="{{ route('admin.invoices.pdf-preview',$item->id) }}"
                                                   class="badge bg-danger"><i class="fas fa-file-pdf"></i></a>
                                                <a href="{{ route('admin.labs.show',$item->id) }}"
                                                   class="badge bg-success"><i class="fas fa-eye"></i></a>

                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td>No record Found <a href="{{ route($pageHeader['create_route']) }}"
                                                                   class="badge btn-info">Create</a></td>
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
<script>
    document.getElementById('due_filter').addEventListener('change', function() {
        const dueValue = this.value;
        const currentUrl = new URL(window.location.href);

        // Update the URL with the new 'due' query parameter
        currentUrl.searchParams.set('due', dueValue);

        // Redirect to the updated URL
        window.location.href = currentUrl.toString();
    });
</script>
<script>
    document.getElementById('dueToggle').addEventListener('change', function () {
        const value = this.value;
        document.getElementById('normalTable').style.display = value === 'due' ? 'none' : '';
        document.getElementById('dueTable').style.display = value === 'due' ? '' : 'none';
    });
</script>
@endpush

