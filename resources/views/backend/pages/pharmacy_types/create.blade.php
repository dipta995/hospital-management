@extends('backend.layouts.master')
@section('title')
    Create {{ $pageHeader['title'] }}
@endsection
@push('styles')
    @include('backend.layouts.partials.crud-styles')
    @include('backend.layouts.partials.pharmacy-styles')
@endpush
@section('admin-content')
    @include('backend.layouts.partials.pharmacy-simple-form', [
        'formTitle' => 'Add Pharmacy Type',
        'formSubtitle' => 'Tablet, syrup, injection, etc.',
        'formIcon' => 'fa-layer-group',
        'formAction' => route($pageHeader['store_route']),
    ])
@endsection
