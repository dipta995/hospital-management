@extends('backend.layouts.master')

@section('title')
    Edit {{ $pageHeader['title'] }} Item
@endsection

@push('styles')
    <!-- Add any custom styles if needed -->
@endpush

@section('admin-content')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit {{ $pageHeader['title'] }} Item</h4>
                        @include('backend.layouts.partials.message')
                        <form method="post" action="{{ route('admin.purchases.update-item', $edited->id) }}">
                            @csrf
                            @method('POST')
                            <fieldset>
                                <div class="form-group col-md-4">
                                    <label>Quantity</label>
                                    <input type="number" name="quantity" value="{{ old('quantity',$edited->quantity) }}" class="form-control">
                                </div><div class="form-group col-md-4">
                                    <label>Quantity Spend</label>
                                    <input type="number" name="quantity_spend" max="{{ $edited->quantity }}" value="{{ old('quantity',$edited->quantity_spend) }}" class="form-control">
                                </div>
                                <button type="submit" class="btn btn-success float-end mt-2">Update</button>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@endpush
