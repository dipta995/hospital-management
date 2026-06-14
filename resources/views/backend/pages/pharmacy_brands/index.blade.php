@extends('backend.layouts.master')
@section('title') {{ $pageHeader['title'] }} @endsection
@push('styles')
    @include('backend.layouts.partials.crud-styles')
    @include('backend.layouts.partials.pharmacy-styles')
@endpush
@section('admin-content')
    @php $userGuard = Auth::guard('admin')->user(); $perm = 'pharmacy_brands'; @endphp
    <div class="crud-page pharm-page container-fluid py-3">
        @include('backend.layouts.partials.crud-hero', ['heroTitle' => $pageHeader['title'], 'heroSubtitle' => 'Pharmacy brands', 'heroIcon' => 'fa-trademark', 'heroCreateRoute' => $userGuard->can("{$perm}.create") ? $pageHeader['create_route'] : null, 'heroCreateLabel' => 'Add Brand'])
        <div class="crud-card">
            @include('backend.layouts.partials.message')
            <div class="crud-table-wrap"><div class="table-responsive">
                <table class="table crud-table table-hover mb-0">
                    <thead><tr><th>#</th><th>Name</th><th class="text-end">Action</th></tr></thead>
                    <tbody>
                    @forelse($datas as $item)
                        <tr id="table-data{{ $item->id }}">
                            <td>{{ $loop->iteration }}</td><td><strong>{{ $item->name }}</strong></td>
                            <td class="text-end"><div class="crud-action-group justify-content-end">
                                @if($userGuard->can("{$perm}.edit"))<a href="{{ route($pageHeader['edit_route'], $item->id) }}" class="crud-btn-icon crud-btn-edit"><i class="fas fa-pen"></i></a>@endif
                                @if($userGuard->can("{$perm}.delete"))<a href="javascript:void(0)" class="crud-btn-icon crud-btn-delete" onclick="dataDelete({{ $item->id }},'{{ $pageHeader['base_url'] }}')"><i class="fas fa-trash"></i></a>@endif
                            </div></td>
                        </tr>
                    @empty<tr><td colspan="3" class="crud-empty">No records.</td></tr>@endforelse
                    </tbody>
                </table>
            </div></div>
            <div class="d-flex justify-content-end mt-3">{!! $datas->links() !!}</div>
        </div>
    </div>
@endsection
