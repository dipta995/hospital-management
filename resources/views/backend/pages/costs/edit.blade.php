@extends('backend.layouts.master')
@section('title')
    Edit {{ $pageHeader['title'] }}
@endsection

@push('styles')
    @include('backend.layouts.partials.crud-styles')
    @include('backend.layouts.partials.cost-category-select2-assets')
@endpush

@section('admin-content')
    <div class="crud-page container-fluid py-3">
        @include('backend.layouts.partials.crud-form-hero', [
            'formTitle' => 'Edit Cost',
            'formSubtitle' => \Illuminate\Support\Str::limit($edited->reason, 60),
            'formIcon' => 'fa-pen-to-square',
        ])

        <div class="crud-card">
            @include('backend.layouts.partials.message')

            <form method="post" action="{{ route($pageHeader['update_route'], $edited->id) }}">
                @method('PUT')
                @csrf

                <div class="crud-form-section">
                    <div class="crud-form-section-header">
                        <i class="fas fa-receipt"></i> Cost Details
                    </div>
                    <div class="crud-form-section-body">
                        <div class="row crud-form-grid g-3">
                            <div class="col-md-6">
                                <x-default.label required="true" for="cost_category_id">Category</x-default.label>
                                @include('backend.layouts.partials.cost-category-select', [
                                    'categories' => $categories,
                                    'selected' => old('cost_category_id', $edited->cost_category_id),
                                ])
                                <x-default.input-error name="cost_category_id"></x-default.input-error>
                                <small class="text-muted">Type to search category</small>
                            </div>
                            <div class="col-md-6">
                                <x-default.label required="true" for="reason">Reason</x-default.label>
                                <x-default.input name="reason" class="form-control" id="reason" type="text"
                                                 value="{{ old('reason', $edited->reason) }}"></x-default.input>
                                <x-default.input-error name="reason"></x-default.input-error>
                            </div>
                            <div class="col-md-6">
                                <x-default.label required="true" for="amount">Amount (৳)</x-default.label>
                                <x-default.input name="amount" class="form-control" id="amount" type="number" step="0.01" min="0.01"
                                                 value="{{ old('amount', $edited->amount) }}"></x-default.input>
                                <x-default.input-error name="amount"></x-default.input-error>
                            </div>
                            <div class="col-md-6">
                                <x-default.label for="creation_date_display">Date</x-default.label>
                                <input type="text" class="form-control" id="creation_date_display" readonly
                                       value="{{ $edited->creation_date ? \Carbon\Carbon::parse($edited->creation_date)->format('d M Y') : '—' }}">
                                <small class="text-muted">Date is set at creation time</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="crud-form-actions">
                    <a href="{{ route($pageHeader['index_route']) }}" class="btn-crud-cancel">Cancel</a>
                    <button type="submit" class="btn btn-crud-submit">Update Cost</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#cost_category_id').select2({
                placeholder: 'Search or select category...',
                allowClear: true,
                width: '100%',
                minimumResultsForSearch: 0
            });
        });
    </script>
@endpush
