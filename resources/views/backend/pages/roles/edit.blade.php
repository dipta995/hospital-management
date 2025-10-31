@extends('backend.layouts.master')
@section('title')
    Edit role
@endsection
@push('styles')

@endpush
@section('admin-content')
    <!-- partial -->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Modify  <strong>{{ $edited->name }}'s</strong> Information</h4>
                            <form class="cmxform" method="post" action="{{ route($pageHeader['update_route'], $edited->id) }}">
                                @method('PUT')
                                @csrf
                                <fieldset>
                                    <div class="form-group">
                                        <label for="name">Name <strong class="text-danger">*</strong></label>
                                        <input id="name" class="form-control @error('name') is-invalid @enderror" name="name" type="text" value="{{ old('name',$edited->name) }}">
                                        @error('name')
                                        <strong class="text-danger">{{ $errors->first('name') }}</strong>
                                        @enderror
                                    </div>
                                    @foreach ($permission_groups as $group)
                                        <div class="row">
                                            @php  $i = 1;  @endphp
                                            <div class="col-md-4">
                                                <div class="form-check form-switch">
                                                    {{-- <input value="" class="form-check-input" name="" type="checkbox" id="flexSwitchCheckDefault"> --}}
                                                    <label class="form-check-label"
                                                           for="flexSwitchCheckDefault">{{ $group->name }}</label>
                                                </div>
                                            </div>
                                            <div class="col-md-7">
                                                @php
                                                    // $permissions = DB::('permissions')->getpermissionsByGroupName($group->name);
                                                    $j = 1;
                                                @endphp
                                                @foreach ($permissions as $permission)
                                                    @if ($permission->group_name == $group->name)
                                                        <div class="form-check form-switch">
                                                            <input
                                                                {{ $edited->hasPermissionTo($permission->name) == 1 ? 'checked' : '' }}
                                                                value="{{ $permission->id }}" class="form-check-input"
                                                                name="permissions[]" type="checkbox"
                                                                id="flexSwitchCheckDefault">
                                                            <label class="form-check-label"
                                                                   for="flexSwitchCheckDefault">{{ $permission->name }}</label>
                                                        </div>
                                                    @endif
                                                    @php
                                                        $j++;
                                                    @endphp
                                                @endforeach
                                                <hr>
                                            </div>
                                        </div>
                                        @php
                                            $i++;
                                        @endphp
                                    @endforeach

{{--                                     <div class="form-check form-switch">--}}
{{--                                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked>--}}
{{--                                    <label class="form-check-label" for="flexSwitchCheckChecked">Checked switch checkbox--}}
{{--                                        input</label>--}}
{{--                                </div> --}}
                                    <x-default.button class="float-end mt-2 btn-success">Update</x-default.button>

                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
        
        <!-- partial -->
    </div>
@endsection

@push('scripts')

@endpush
