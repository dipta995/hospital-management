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
                            <h4 class="card-title">{{ $pageHeader['title'] }}'s List</h4>

                            <p class="card-description">
                                @include('backend.layouts.partials.message')
                            </p>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Doctor</th>
                                        <th>Room</th>
                                        <th>Access Link</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($datas as  $key =>$item)
                                        <tr id="table-data{{ $item->id }}">
                                            <td>{{\App\Helper\CustomHelper::startIndexDynamic($datas) + $key }}</td>
                                            <td>{{ $item->doctor->name }}</td>
                                            <td>{{ $item->room_no }}</td>
                                            <td>
                                                <div class="mb-3 p-3 border rounded bg-light">
                                                    <label class="fw-bold mb-1">Doctor Link:</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control copy-input" value="{{ route('doctor.serials.index', $item->secret_unique_code) }}" readonly>
                                                        <button class="btn btn-outline-primary copy-btn">📋 Copy</button>
                                                    </div>
                                                </div>

                                                <div class="p-3 border rounded bg-light">
                                                    <label class="fw-bold mb-1">Public Link:</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control copy-input" value="{{ route('doctor.serials.indexPublic', $item->secret_unique_code) }}" readonly>
                                                        <button class="btn btn-outline-primary copy-btn">📋 Copy</button>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <a href="{{ route($pageHeader['edit_route'],$item->id) }}"
                                                   class="badge bg-info"><i class="fas fa-pen"></i></a>
                                                <a class="badge bg-danger" href="javascript:void(0)"
                                                   onclick="dataDelete({{ $item->id }},'{{ $pageHeader['base_url'] }}')"><i
                                                        class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td>No record Found <a href="{{ route($pageHeader['create_route']) }}"
                                                                   class="btn btn-info">Create</a></td>
                                        </tr>
                                    @endforelse

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            $(".copy-btn").click(function () {
                let inputField = $(this).prev("input");
                inputField.select();
                document.execCommand("copy");

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Copied to clipboard!',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                });

                // Temporary button feedback
                let button = $(this);
                button.html("✅ Copied").removeClass("btn-outline-primary").addClass("btn-success");

                setTimeout(() => {
                    button.html("📋 Copy").removeClass("btn-success").addClass("btn-outline-primary");
                }, 1500);
            });
        });
    </script>


@endpush
