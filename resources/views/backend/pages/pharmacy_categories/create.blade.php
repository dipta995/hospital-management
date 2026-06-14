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
        'formTitle' => 'Add Pharmacy Category',
        'formSubtitle' => 'Group medicines for reporting & filters',
        'formIcon' => 'fa-folder-open',
        'formAction' => route($pageHeader['store_route']),
        'showDescription' => true,
    ])
@endsection
