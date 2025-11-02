@extends('backend.layouts.master')

@section('title')
    Edit {{ $pageHeader['title'] }}
@endsection

@section('admin-content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Edit Admit Information</h4>

                        @include('backend.layouts.partials.message')

                        <form method="POST" action="{{ route($pageHeader['update_route'], $edited->id) }}">
                            @csrf
                            @method('PUT')

                           <div class="form-group mb-3">
                                <label>Patient Name</label>
                                <input type="text" class="form-control" value="{{ optional($edited->user)->name ?? 'Unknown' }}" disabled>
                            </div>

                            <div class="form-group mb-3">
                                <label for="admit_at">Admit Date <strong class="text-danger">*</strong></label>
                                <input type="date" id="admit_at" name="admit_at" class="form-control"
                                       value="{{ old('admit_at', $edited->admit_at ? date('Y-m-d', strtotime($edited->admit_at)) : '') }}" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="release_at">Release Date</label>
                                <input type="date" id="release_at" name="release_at" class="form-control"
                                       value="{{ old('release_at', $edited->release_at ? date('Y-m-d', strtotime($edited->release_at)) : '') }}">
                            </div>

                            <div class="form-group mb-3">
                                <label for="nid">NID Number</label>
                                <input type="text" id="nid" name="nid" class="form-control" 
                                       value="{{ old('nid', $edited->nid) }}">
                            </div>


                            <div class="form-group mb-3">
                                <label for="note">Note</label>
                                <textarea id="note" name="note" class="form-control" rows="4">{{ old('note', $edited->note) }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-success float-end">
                                <i class="fas fa-save"></i> Update Admit
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
