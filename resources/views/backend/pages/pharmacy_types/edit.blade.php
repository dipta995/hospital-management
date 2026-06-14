@extends('backend.layouts.master')
@section('title')
    Edit {{ $pageHeader['title'] }}
@endsection
@push('styles')
    @include('backend.layouts.partials.crud-styles')
    @include('backend.layouts.partials.pharmacy-styles')
@endpush
@section('admin-content')
    @include('backend.layouts.partials.pharmacy-simple-form', [
        'formTitle' => 'Edit Pharmacy Type',
        'formSubtitle' => $edited->name,
        'formIcon' => 'fa-layer-group',
        'formAction' => route($pageHeader['update_route'], $edited->id),
        'formMethod' => 'PUT',
        'edited' => $edited,
    ])
@endsection
