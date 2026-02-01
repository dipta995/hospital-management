@extends('backend.layouts.master')
@section('title')
    Edit {{ $pageHeader['title'] }}
@endsection
@push('style')

    <style>
        #ordered-services tfoot td {
            vertical-align: middle;
        }

        #ordered-services tfoot td input,
        #ordered-services tfoot td span {
            width: 100%;
        }

        #ordered-services tfoot td:nth-child(1),
        #ordered-services tfoot td:nth-child(2) {
            width: 30%;
        }

        #ordered-services tfoot td:nth-child(3),
        #ordered-services tfoot td:nth-child(4) {
            width: 70%;
        }

        #ordered-services tfoot td input {
            width: 100%;
            padding: 10px;
        }

        #ordered-services tfoot {
            text-align: left;
        }

        #discount-percent-row,
        #discount-taka-row {
            display: contents;
            justify-content: space-between;
            align-items: center;
        }

        #discount-percent-row input,
        #discount-taka-row input {
            width: 60%;
            margin-right: 10px;
        }
        #ordered-services tr th,td{
            padding: 5px !important;
        }
    </style>
@endpush
@section('admin-content')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Edit {{ $pageHeader['title'] }}</h4>
                            @include('backend.layouts.partials.message')

                            <form method="POST" action="{{ route($pageHeader['update_route'], $edited->id) }}" id="receipt-form">
                                @csrf
                                @method('PUT')
                                <fieldset>
                                    <input type="hidden" id="customer_id" name="customer_id" value="{{ $edited->user_id }}">
                                    <input type="hidden" id="admit_id" name="admit_id" value="{{ $edited->admit_id }}">

                                    <h4 class="card-title bg-info p-1 mt-3 mb-3">Patient Details</h4>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-default.label for="patient_name">Patient Name</x-default.label>
                                                <x-default.input readonly name="patient_name" value="{{ $edited->user->name ?? '' }}" class="form-control" id="patient_name" type="text" />
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-default.label for="age_year">Age</x-default.label>
                                                <x-default.input readonly name="age_year" value="{{ $edited->user->age ?? '' }}" class="form-control" id="age_year" type="text" />
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-default.label for="phone">Phone</x-default.label>
                                                <x-default.input readonly name="phone" value="{{ $edited->user->phone ?? '' }}" class="form-control" id="phone" type="text" />
                                            </div>
                                        </div>
                                    </div>

                                    <h4 class="card-title bg-info p-1 mt-3 mb-3">Service Category</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <select class="form-control" name="service_category_id" id="service_category_id">
                                                <option value="">Choose</option>
                                                @foreach($service_categories as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <h4 class="card-title bg-info p-1 mt-3 mb-3">Services</h4>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <x-default.label required="true" for="service_name">Service Name <span class="text-danger">*</span></x-default.label>
                                                <x-default.input name="service_name" class="form-control" id="service_name" type="text" />
                                                <input type="hidden" name="service_id" id="service_id">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-default.label required="true" for="price">Price <span class="text-danger">*</span></x-default.label>
                                                <x-default.input name="price" class="form-control" id="price" type="number" />
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <x-default.button class="float-end mt-2 btn-success" id="add-service">Add Service</x-default.button>
                                        </div>
                                    </div>

                                    <h4 class="card-title bg-info p-1 mt-3 mb-3">Invoice Details</h4>
                                    <table id="ordered-services" class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Test Name</th>
                                            <th>Price</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td><strong></strong></td>
                                            <td><strong>Subtotal</strong></td>
                                            <td id="subtotal" >0</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><strong>Discount Choose</strong></td>
                                            <td colspan="3">
                                                <label><input type="radio" id="discount-choose" name="discount-type" value="percent"> Percent(%)</label>
                                                <label><input type="radio" id="discount-choose" name="discount-type" value="taka" checked> TK </label>
                                            </td>
                                        </tr>
                                        <tr id="discount-percent-row" style="display: none;">
                                            <td colspan="2"><strong>Discount (%)</strong></td>
                                            <td><input type="number" id="discount-percent" class="form-control" value="0"></td>
                                            <td colspan="2"><strong>Discount Amount:</strong> <span id="discount-amount">0</span></td>
                                        </tr>
                                        <tr id="discount-taka-row">
                                            <td colspan="2"><strong>Discount (TK)</strong></td>
                                            <td><input type="number" id="discount-taka" class="form-control" value="{{ $edited->discount_amount }}"></td>
                                            <td colspan="2"><strong>Discount Amount:</strong> <span id="discount-amount-taka">0</span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><strong>Paid</strong></td>
                                            <td colspan="3"><input type="number" id="paid-amount" value="{{ $edited->receptPayments->sum('paid_amount') }}" class="form-control"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><strong>Due</strong></td>
                                            <td id="due-amount" colspan="3">0</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><strong>Final Amount</strong></td>
                                            <td colspan="3">
                                                <span id="final-amount" >{{ $edited->total_amount }}</span>
                                            </td>
                                        </tr>
                                        </tfoot>
                                    </table>

                                    <x-default.button class="float-end mt-2 btn-success btn-store-data">Update</x-default.button>
                                </fieldset>
                            </form>

                            <div class="success-pdf mt-3"></div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @php
        $existingServices = $edited->receptList->map(function($row) {
            return [
                'service_id' => $row->service_id,
                'name' => $row->service->name ?? '',
                'price' => (float) $row->price,
            ];
        })->values()->toArray();
    @endphp

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

    <script>
        $(document).ready(function () {

            $.ajaxSetup({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
            });
            let selectedRecepts = [];

            // preload existing services
            const existingServices = @json($existingServices);
            if (existingServices && existingServices.length) {
                selectedRecepts = existingServices;
                updateTable();
            }

            function configureAutocomplete(fieldId, sourceUrl, onSelectCallback) {
                $(`#${fieldId}`).autocomplete({
                    source: function (request, response) {
                        if (request.term.trim() === "") {
                            response([]);
                            return;
                        }
                        $.ajax({
                            url: sourceUrl,
                            type: "GET",
                            data: { query: request.term },
                            success: function (data) {
                                response(data.map(item => ({
                                    label: item.name,
                                    value: item.name,
                                    id: item.serviceID,
                                    price: item.price
                                })));
                            }
                        });
                    },
                    select: function (event, ui) {
                        onSelectCallback(ui.item);
                    },
                    minLength: 1
                });
            }

            configureAutocomplete("service_name", "/admin/get-services", function (item) {
                $("#service_id").val(item.id);
                $("#price").val(item.price);
            });

            $("input[name='discount-type']").on("change", function () {
                const selectedDiscountType = $("input[name='discount-type']:checked").val();
                if (selectedDiscountType === "percent") {
                    $("#discount-taka").val(0);
                    $("#discount-percent-row").show();
                    $("#discount-taka-row").hide();
                } else if (selectedDiscountType === "taka") {
                    $("#discount-percent").val(0);
                    $("#discount-percent-row").hide();
                    $("#discount-taka-row").show();
                }
                updateSummary();
            });

            function addService() {
                const service_id = $("#service_id").val().trim();
                const name = $("#service_name").val().trim();
                const price = parseFloat($("#price").val().trim());
                if (!service_id || !name || isNaN(price)) {
                    alert("Please fill all service fields.");
                    return;
                }

                const isDuplicate = selectedRecepts.some(s => s.service_id === service_id);
                if (isDuplicate) {
                    alert("This service is already added.");
                    return;
                }

                selectedRecepts.push({ service_id, name, price });
                updateTable();
                $("#service_id").val("");
                $("#service_name").val("");
                $("#price").val("");
            }

            $("#add-service").click(function (e) {
                e.preventDefault();
                addService();
            });

            $("#service_name, #price").on("keydown", function (e) {
                if (e.keyCode === 13) {
                    e.preventDefault();
                    addService();
                }
            });

            function updateTable() {
                let tbody = $("#ordered-services tbody");
                tbody.empty();
                let subtotal = 0;

                selectedRecepts.forEach((s, index) => {
                    subtotal += parseFloat(s.price);
                    tbody.append(`
                <tr>
                    <td>${index + 1}</td>
                    <td>${s.name}</td>
                    <td>${parseFloat(s.price).toFixed(2)}</td>
                    <td><button class="btn btn-danger btn-sm remove-service" data-index="${index}">Remove</button></td>
                </tr>
            `);
                });

                $("#subtotal").text(subtotal.toFixed(2));
                updateSummary();
            }

            function updateSummary() {
                let subtotal = selectedRecepts.reduce((sum, s) => sum + parseFloat(s.price), 0);
                const discountPercent = parseFloat($("#discount-percent").val()) || 0;
                const discountTaka = parseFloat($("#discount-taka").val()) || 0;
                const paidAmount = parseFloat($("#paid-amount").val()) || 0;

                let discount = discountTaka > 0 ? discountTaka : (subtotal * discountPercent / 100);
                let final = subtotal - discount;
                let due = final - paidAmount;

                $("#discount-amount").text(discount.toFixed(2));
                $("#final-amount").text(final.toFixed(2));
                $("#due-amount").text(due.toFixed(2));
            }

            $("#discount-percent, #discount-taka, #paid-amount").on("input", updateSummary);

            $(document).on("click", ".remove-service", function () {
                const index = $(this).data("index");
                selectedRecepts.splice(index, 1);
                updateTable();
            });

            $(document).on("click", ".btn-store-data", function (e) {
                e.preventDefault();

                const services = selectedRecepts;
                const customerDetails = {
                    customer_id: $("#customer_id").val(),
                    admit_id: $("#admit_id").val(),
                    discount_by: "admin"
                };
                const paymentDetails = {
                    paid_amount: parseFloat($("#paid-amount").val()) || 0,
                    discount_amount: parseFloat($("#discount-amount").text()) || 0,
                    total_amount: parseFloat($("#final-amount").text()) || 0
                };

                if (!services.length) {
                    alert("Please fill all required fields.");
                    return;
                }

                $.ajax({
                    url: "{{ route('admin.recepts.update', $edited->id) }}",
                    type: "POST",
                    contentType: "application/json",
                    data: JSON.stringify({ _method: "PUT", services, customerDetails, paymentDetails }),
                    success: function (response) {
                        alert("Recept updated successfully!");
                        location.reload();
                    },
                    error: function () {
                        alert("Failed to update the recept. Please try again.");
                    }
                });
            });

            // initialize summary with current values
            updateTable();
        });
    </script>
@endpush
