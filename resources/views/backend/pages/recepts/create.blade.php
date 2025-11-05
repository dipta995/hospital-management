@extends('backend.layouts.master')
@section('title')
    Create New {{ $pageHeader['title'] }}
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
            width: 30%; /* Left column (inputs) */
        }

        #ordered-services tfoot td:nth-child(3),
        #ordered-services tfoot td:nth-child(4) {
            width: 70%; /* Right column (summary) */
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
                            <h4 class="card-title">Create New {{ $pageHeader['title'] }}</h4>
                            @include('backend.layouts.partials.message')

                            <form method="POST" action="{{ route($pageHeader['store_route']) }}" id="receipt-form">
                                @csrf
                                <fieldset>
                                    <input type="hidden" id="customer_id" name="customer_id" value="{{ $user_data->id }}">
                                    <!-- ================= Patient Details ================= -->
                                    <h4 class="card-title bg-info p-1 mt-3 mb-3">Patient Details</h4>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-default.label required="true" for="patient_name">Patient Name <span class="text-danger">*</span></x-default.label>
                                                <x-default.input name="patient_name" value="{{ old('patient_name', $user_data->name) }}" class="form-control" id="patient_name" type="text" />
                                                <x-default.input-error name="patient_name"></x-default.input-error>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-default.label required="true" for="age_year">Age <span class="text-danger">*</span></x-default.label>
                                                <x-default.input name="age_year" value="{{ old('age_year', $user_data->age) }}" class="form-control" id="age_year" type="text" />
                                                <x-default.input-error name="age_year"></x-default.input-error>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-default.label required="true" for="phone">Phone <span class="text-danger">*</span></x-default.label>
                                                <x-default.input name="phone" value="{{ old('phone', $user_data->phone) }}" class="form-control" id="phone" type="text" />
                                                <x-default.input-error name="phone"></x-default.input-error>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-default.label for="blood_group">Blood Group</x-default.label>
                                                <x-default.input name="blood_group" value="{{ old('blood_group', $user_data->blood_group) }}" class="form-control" id="blood_group" type="text" />
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-default.label for="gender">Gender</x-default.label>
                                                <select class="form-control" name="gender" id="gender">
                                                    <option @selected($user_data->gender == 'Male') value="Male">Male</option>
                                                    <option @selected($user_data->gender == 'Female') value="Female">Female</option>
                                                    <option @selected($user_data->gender == 'Other') value="Other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-default.label for="address">Address</x-default.label>
                                                <x-default.input name="address" value="{{ old('address', $user_data->address) }}" class="form-control" id="address" type="text" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ================= Service Details ================= -->
                                    <h4 class="card-title bg-info p-1 mt-3 mb-3">Services</h4>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <x-default.label required="true" for="service_name">Service Name <span class="text-danger">*</span></x-default.label>
                                                <x-default.input name="service_name" class="form-control" id="service_name" type="text" />
                                                <input type="hidden" name="service_id" id="service_id">
                                                <x-default.input-error name="service_name"></x-default.input-error>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-default.label required="true" for="price">Price <span class="text-danger">*</span></x-default.label>
                                                <x-default.input name="price" class="form-control" id="price" type="number" />
                                                <x-default.input-error name="price"></x-default.input-error>
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
                                            {{--                                            <th>Refer Fee</th>--}}
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <!-- Rows will be dynamically added here -->
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td><strong></strong></td>
                                            <td><strong>Subtotal</strong></td>
                                            <td id="subtotal" >0</td>
                                            {{--                                            <td id="refer-fee-total" >0</td>--}}
                                            <td></td>

                                        </tr>

                                        <!-- Discount Choose section -->
                                        <tr>
                                            <td colspan="2"><strong>Discount Choose</strong></td>
                                            <td colspan="3">
                                                <label><input type="radio" id="discount-choose" name="discount-type" value="percent"> Percent(%)</label>
                                                <label><input type="radio" id="discount-choose" name="discount-type" value="taka"> TK </label>
                                            </td>
                                        </tr>

                                        <!-- Discount Percentage Section -->
                                        <tr id="discount-percent-row">
                                            <td colspan="2"><strong>Discount (%)</strong></td>
                                            <td><input type="number" id="discount-percent" class="form-control" value="0"></td>
                                            <td colspan="2"><strong>Discount Amount:</strong> <span id="discount-amount">0</span></td>
                                        </tr>

                                        <!-- Discount Taka Section -->
                                        <tr id="discount-taka-row" style="display: none;">
                                            <td colspan="2"><strong>Discount (TK)</strong></td>
                                            <td><input type="number" id="discount-taka" class="form-control" value="0"></td>
                                            <td colspan="2"><strong>Discount Amount:</strong> <span id="discount-amount-taka">0</span></td>
                                        </tr>

                                        <!-- Discount By Section -->
                                        <tr>
                                            <td colspan="2"><strong>Discount By:</strong></td>
                                            <td colspan="3"><input type="text" name="discount_by" value="{{ auth()->user()->name }}" id="discount-by" class="form-control"></td>
                                        </tr>

                                        <!-- Paid Amount Section -->
                                        <tr>
                                            <td colspan="2"><strong>Paid</strong></td>
                                            <td colspan="3"><input type="number" id="paid-amount" class="form-control"></td>
                                        </tr>

                                        <!-- Due Amount Section -->
                                        <tr>
                                            <td colspan="2"><strong>Due</strong></td>
                                            <td id="due-amount" colspan="3">0</td>
                                        </tr>

                                        <!-- Final Amount Section -->
                                        <tr>
                                            <td colspan="2"><strong>Final Amount</strong></td>
                                            <td colspan="3">
                                                <span id="final-amount" >0</span>
                                                (<span id="final-refer">0</span>)
                                            </td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                    <x-default.button class="float-end mt-2 btn-success btn-store-data">Create</x-default.button>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

    <script>
        $(document).ready(function () {

            $.ajaxSetup({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
            });
            let selectedRecepts = [];
            // ================= Autocomplete =================
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
                updateSummary(); // Update summary after selecting discount type
            });

            // ================= Add Service =================
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

            // ================= Update Table =================
            function updateTable() {
                let tbody = $("#ordered-services tbody");
                tbody.empty();
                let subtotal = 0;

                selectedRecepts.forEach((s, index) => {
                    subtotal += s.price;
                    tbody.append(`
                <tr>
                    <td>${index + 1}</td>
                    <td>${s.name}</td>
                    <td>${s.price.toFixed(2)}</td>
                    <td><button class="btn btn-danger btn-sm remove-service" data-index="${index}">Remove</button></td>
                </tr>
            `);
                });

                $("#subtotal").text(subtotal.toFixed(2));
                updateSummary();
            }

            // ================= Summary =================
            function updateSummary() {
                let subtotal = selectedRecepts.reduce((sum, s) => sum + s.price, 0);
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

            // ================= Submit Data =================
            $(".btn-store-data").click(function (e) {
                e.preventDefault();

                const services = selectedRecepts;
                const customerDetails = {
                    customer_id: $("#customer_id").val(),
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
                    url: "{{ route('admin.recepts.store') }}",
                    type: "POST",
                    contentType: "application/json",
                    data: JSON.stringify({ services, customerDetails, paymentDetails }),
                    success: function (response) {

                        // alert("Order saved successfully!");
                        if(response.recept_id){
                            const receptId = response.recept_id;
                            const customer_name = response.customer_name;
                            const receptUrl = `{{ route('admin.recepts.pdf-preview', ':id') }}`.replace(':id', receptId);
                            localStorage.removeItem('receptMessage');
                            // Save the message in localStorage
                            localStorage.setItem('receptMessage', `<p class="alert alert-success text-center">"${customer_name}" new recept is available: <a class="btn btn-danger" href="${receptUrl}" target="_blank">Click <i class="fas fa-print"></i></a></p>`);

                            location.reload();
                        }
                        else{
                            localStorage.setItem('receptMessage', `<p class="alert alert-danger text-center">Something Went wrong try again</p>`);

                        }
                    },
                    error: function (error) {
                        alert("Failed to save the order. Please try again.");
                    }
                });
            });

            // ================= LocalStorage Message =================
            const message = localStorage.getItem('receptMessage');
            if (message) {
                $('.success-pdf').html(message);
                localStorage.removeItem('receptMessage');
            }

        });
    </script>
@endpush
