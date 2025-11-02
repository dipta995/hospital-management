@extends('backend.layouts.master')
@section('title')
    {{ $pageHeader['title'] }}
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
                            <form action="" class="row" autocomplete="off" method="get">
                                <div class="col-md-6">
                                    <input type="text" id="search" name="query" placeholder="Enter your phone no" class="search form-control">
                                    <div id="suggestions" class="list-group position-absolute" style="z-index: 1000;"></div>
                                </div>
                            </form>

                            <p class="card-description">
                                @include('backend.layouts.partials.message')
                            </p>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($datas as $item)
                                        <tr id="table-data{{ $item->id }}">
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->phone }}</td>
                                            <td>{{ $item->address }}</td>
                                            <td>
                                                <a href="{{ route('admin.invoices.create').'?for='.$item->id }}" class="btn bg-success text-white"><i class="fa fa-flask"
                                                                                                aria-hidden="true"></i>
                                                    <a href="{{ route('admin.admits.create').'?for='.$item->id }}" class="btn bg-dark text-white"><i class="fa fa-bed"
                                                                                                 aria-hidden="true"></i>
                                                    </a>   <a href="{{ route('admin.recepts.create').'?for='.$item->id }}" class="btn bg-info text-white"><i class="fa fa-pager"
                                                                                                 aria-hidden="true"></i>
                                                    </a>
                                                    <a href="{{ route($pageHeader['edit_route'],$item->id) }}"
                                                       class="badge bg-info"><i class="fas fa-pencil"></i></a>
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
                                                                   class="badge btn-info">Create</a></td>
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
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script>
        $(document).ready(function() {
            function configureAutocomplete(fieldId, sourceUrl, onSelectCallback) {
                let enterEnabled = false;

                $(`#${fieldId}`).autocomplete({
                    source: function (request, response) {
                        if (!request.term.trim()) return response([]);
                        $.ajax({
                            url: sourceUrl,
                            type: "GET",
                            dataType: "json",
                            data: { query: request.term },
                            success: function (data) {
                                response(data.map(item => ({
                                    label: `${item.name} (${item.phone})`,
                                    value: item.phone,
                                    ...item
                                })));
                            },
                            error: function () {
                                console.log("Error fetching data");
                            }
                        });
                    },
                    minLength: 1,
                    select: function (event, ui) {
                        $(`#${fieldId}`).val(ui.item.phone);
                        enterEnabled = true;
                        if (typeof onSelectCallback === "function") onSelectCallback(ui.item);
                        return false;
                    },
                    close: function() {
                        enterEnabled = true;
                    }
                });

                // Enter key
                $(`#${fieldId}`).on('keydown', function(e) {
                    if (e.key === "Enter" && enterEnabled) {
                        e.preventDefault();
                        const phone = $(this).val().trim();
                        if (phone) {
                            searchByPhone(phone);
                            enterEnabled = false;
                        }
                    }
                });
            }

            function searchByPhone(phone) {
                console.log("Searching for:", phone);
                // Example AJAX
                /*
                $.get('/admin/search-phone-info', { phone }, function(data) {
                    // handle result
                });
                */
            }

            configureAutocomplete("search", "/admin/search-phone", function(item) {
                $("#phone").val(item.phone);
                $("#user_id").val(item.id);
            });
        });



    </script>
@endpush
