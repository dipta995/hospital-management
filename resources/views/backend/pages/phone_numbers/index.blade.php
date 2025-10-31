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
                                    <h3>Import Phone No</h3>
                                    <form method="post" action="{{ route('admin.phone_numbers-upload')  }}"  class="row" enctype="multipart/form-data" style="padding: 5px; border: 2px solid red;">
                                  @csrf
                                    <div class="form-group col-md-6">
                                        <x-default.label required="true" for="number_category_id">Category</x-default.label>
                                        <select class="form-control" name="number_category_id" id="number_category_id">
                                            <option value="">--Choose--</option>
                                            @foreach($numberCategories as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-default.input-error name="number_category_id"></x-default.input-error>
                                    </div>
                                        <div class="form-group col-md-6">
                                            <x-default.label required="true" for="numbers">Phone Number</x-default.label>
                                            <x-default.input name="numbers" class="form-control" id="numbers" type="file"></x-default.input>
                                            <x-default.input-error name="numbers"></x-default.input-error>
                                        </div>
                                        <x-default.button class="float-end mt-2 btn-success">Upload</x-default.button>

                                    </form>
                                    <p class="card-description">
                                        @include('backend.layouts.partials.message')
                                    </p>
    <div class="card">
        <h4 class="card-title">{{ $pageHeader['title'] }}'s List</h4>
        <form action="" method="GET">
            <div class="form-group">
                <x-default.label required="true" for="number_category_id">Category</x-default.label>
                <select class="form-control" name="number_category_id" id="number_category_id">
                    <option value="">--Choose--</option>
                    @foreach($numberCategories as $item)
                        <option {{ request('number_category_id') == $item->id ? 'selected' : '' }} value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>
                <x-default.button class="float-end mt-2 btn-success">Search</x-default.button>

            </div>
        </form>
    </div>
                                    <div class="table-responsive">
                                        <table class="table table-striped">

                                            <tbody>
                                            <form id="bulkSmsForm" method="POST" action="{{ route('admin.phone_numbers-message') }}">
                                                @csrf
                                                <table class="table table-bordered">
                                                    <thead>
                                                    <tr>
                                                        <th><input type="checkbox" id="selectAll"></th>
                                                        <th>#</th>
                                                        <th>Category</th>
                                                        <th>Name</th>
                                                        <th>Address</th>
                                                        <th>Number</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @forelse($datas as $item)
                                                        <tr id="table-data{{ $item->id }}">
                                                            <td><input type="checkbox" class="row-checkbox" name="selected_ids[]" value="{{ $item->number }}"></td>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $item->numberCategory->name }}</td>
                                                            <td>{{ $item->name }}</td>
                                                            <td>{{ $item->address }}</td>
                                                            <td>{{ $item->number }}</td>
                                                            <td>
                                                                <a href="{{ route($pageHeader['edit_route'], $item->id) }}" class="badge bg-info"><i class="fas fa-pen"></i></a>
                                                                <a class="badge bg-danger" href="javascript:void(0)" onclick="dataDelete({{ $item->id }}, '{{ $pageHeader['base_url'] }}')"><i class="fas fa-trash"></i></a>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5">No record found. <a href="{{ route($pageHeader['create_route']) }}" class="btn btn-info">Create</a></td>
                                                        </tr>
                                                    @endforelse
                                                    </tbody>
                                                </table>

                                                <div class="mt-3">
                                                    <textarea name="sms_message" class="form-control" placeholder="Write SMS message..."></textarea>
                                                    <button type="submit" class="btn btn-success mt-2">Send SMS to Selected</button>
                                                </div>
                                            </form>


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
    <script>
        const selectAllCheckbox = document.getElementById('selectAll');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');

        // When "Select All" is clicked
        selectAllCheckbox.addEventListener('change', function () {
            const isChecked = this.checked;
            rowCheckboxes.forEach(cb => cb.checked = isChecked);
        });

        // If any individual checkbox is unchecked, uncheck "Select All"
        rowCheckboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                if (!this.checked) {
                    selectAllCheckbox.checked = false;
                } else {
                    // If all checkboxes are checked, check "Select All"
                    const allChecked = Array.from(rowCheckboxes).every(c => c.checked);
                    selectAllCheckbox.checked = allChecked;
                }
            });
        });
    </script>

@endpush
