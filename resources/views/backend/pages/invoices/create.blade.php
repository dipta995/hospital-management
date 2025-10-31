@extends('backend.layouts.master')
@section('title')
    Create New {{ $pageHeader['title'] }}
@endsection
@push('styles')

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <style>
        #ordered-products tfoot td {
            vertical-align: middle;
        }

        #ordered-products tfoot td input,
        #ordered-products tfoot td span {
            width: 100%;
        }

        #ordered-products tfoot td:nth-child(1),
        #ordered-products tfoot td:nth-child(2) {
            width: 30%; /* Left column (inputs) */
        }

        #ordered-products tfoot td:nth-child(3),
        #ordered-products tfoot td:nth-child(4) {
            width: 70%; /* Right column (summary) */
        }

        #ordered-products tfoot td input {
            width: 100%;
            padding: 10px;
        }

        #ordered-products tfoot {
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
        #ordered-products tr th,td{
           padding: 5px !important;
        }
    </style>
@endpush
@section('admin-content')
    <!-- partial -->
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Create New {{ $pageHeader['title'] }}</h4>
                            <span class="success-pdf"></span>
                            @include('backend.layouts.partials.message')
{{--                            <form class="cmxform" method="post" action="{{ route($pageHeader['store_route']) }}">--}}
{{--                                @csrf--}}
                                <fieldset>

                                     <h4 class="card-title bg-info p-1 mt-3 mb-3">Patient Details</h4>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-default.label required="true" for="invoice_number">Invoice No</x-default.label>
                                                <x-default.input readonly="readonly" name="invoice_number" value="{{ $invoice_number }}" class="form-control" id="invoice_number" type="text"></x-default.input>
                                                <x-default.input-error name="invoice_number"></x-default.input-error>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-default.label required="true" for="patient_name">Patient Name <span class="text-danger">*</span></x-default.label>
                                                <x-default.input name="patient_name" class="form-control" id="patient_name" type="text"></x-default.input>
                                                <x-default.input-error name="patient_name"></x-default.input-error>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-default.label required="true" for="patient_age_year">Age  <span class="text-danger">*</span></x-default.label>
                                                <x-default.input name="patient_age_year" class="form-control" id="patient_age_year" type="text" ></x-default.input>
                                                <x-default.input-error name="patient_age_year"></x-default.input-error>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-default.label required="true" for="patient_phone">Phone  <span class="text-danger">*</span></x-default.label>
                                                <x-default.input name="patient_phone" class="form-control" id="patient_phone" type="text" ></x-default.input>
                                                <x-default.input-error name="patient_phone"></x-default.input-error>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-default.label required="true" for="patient_blood_group">Blood Group</x-default.label>
                                                <x-default.input name="patient_blood_group" class="form-control" id="patient_blood_group" type="text" ></x-default.input>
                                                <x-default.input-error name="patient_blood_group"></x-default.input-error>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-default.label required="true" for="patient_gender">Gender</x-default.label>
                                                <select class="form-control" name="patient_gender" id="patient_gender">
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                                <x-default.input-error name="patient_gender"></x-default.input-error>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-default.label required="true" for="patient_address">Address</x-default.label>
                                                <x-default.input name="patient_address" class="form-control" id="patient_address" type="text" ></x-default.input>
                                                <x-default.input-error name="patient_address"></x-default.input-error>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-default.label required="true" for="dr_refer_name">Dr Name</x-default.label>
                                                <x-default.input name="dr_refer_name" class="form-control" required id="dr_refer_name" type="text" ></x-default.input>
                                                <x-default.input-error name="dr_refer_name"></x-default.input-error>
                                                <input type="hidden" name="dr_refer_id" id="dr_refer_id">

                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-default.label required="true" for="refer_name">Referred By</x-default.label>
                                                <x-default.input name="refer_name" required class="form-control" id="refer_name" type="text" ></x-default.input>
                                                <x-default.input-error name="refer_name"></x-default.input-error>
                                                <input type="hidden" name="refer_id" id="refer_id">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <div class="form-group">
                                                    <x-default.label required="true" for="delivery_at">Delivery At</x-default.label>
                                                    <x-default.input name="delivery_at" class="form-control" id="delivery_at" value="{{ date('Y-m-d\TH:i', strtotime('+2 hours')) }}" type="datetime-local"> ></x-default.input>
                                                    <x-default.input-error name="delivery_at"></x-default.input-error>
                                                </div>
                                            </div>
                                        </div>
                                        @if(\App\Models\Setting::get('creation_date')=='Yes')
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <div class="form-group">
                                                    <x-default.label required="true" for="creation_date">Posting Date</x-default.label>
                                                    <x-default.input name="creation_date" class="form-control" id="creation_date" value="{{ date('Y-m-d') }}" type="date"> ></x-default.input>
                                                    <x-default.input-error name="creation_date"></x-default.input-error>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    <h4 class="card-title bg-info p-1 mt-3 mb-3">Test's</h4>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <x-default.label required="true" for="name">Name  <span class="text-danger">*</span></x-default.label>
                                                <x-default.input name="name" class="form-control" id="name" type="text"></x-default.input>
                                                <x-default.input-error name="name"></x-default.input-error>
                                            </div>
                                            <input type="hidden" name="product_id" id="product_id" >
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-default.label required="true" for="price">Price  <span class="text-danger">*</span></x-default.label>
                                                <x-default.input name="price" class="form-control" id="price" type="number" ></x-default.input>
                                                <x-default.input-error name="price"></x-default.input-error>
                                            </div>
                                        </div>
{{--                                        <div class="col-md-4">--}}
{{--                                            <div class="form-group">--}}
{{--                                                <x-default.label required="true" for="reefer_fee">Refer Fee  <span class="text-danger">*</span></x-default.label>--}}
{{--                                                <x-default.input name="reefer_fee" class="form-control" id="reefer_fee" type="number" ></x-default.input>--}}
{{--                                                <x-default.input-error name="reefer_fee"></x-default.input-error>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
                                        <div class="col-md-12">
                                            <x-default.button class="float-end mt-2 btn-success" id="add-product">Add</x-default.button>
                                        </div>
                                    </div>
                                    <h4 class="card-title bg-info p-1 mt-3 mb-3">Invoice Details</h4>
                                    <table id="ordered-products" class="table table-bordered">
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
                        </div>
                                    <span class="success-pdf"></span>
                    </div>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- partial -->
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

    <script>
        $(document).ready(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            let selectedProducts = [];

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
                                    value: item.id,
                                    ...item
                                })));
                            },
                            error: function () {
                                alert("Error fetching data.");
                            }
                        });
                    },
                    select: function (event, ui) {
                        onSelectCallback(ui.item);
                    },
                    minLength: 1
                });
            }

            // Configure product autocomplete
            configureAutocomplete("name", "/admin/get-products", function (item) {
                $("#price").val(item.price);
                // $("#reefer_fee").val(item.reefer_fee);
                $("#product_id").val(item.productID);
            });

            // Configure doctors autocomplete
            configureAutocomplete("dr_refer_name", "/admin/get-doctors", function (item) {
                $("#dr_refer_id").val(item.referID);
                $("#dr_refer_name").val(item.name);
            });

            // Configure referrals autocomplete
            configureAutocomplete("refer_name", "/admin/get-referrals", function (item) {
                $("#refer_id").val(item.referID);
                $("#refer_name").val(item.name);
            });

            // Handle radio button change for discount type (percent or taka)
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

            // Add product to the table on "Add" button click
           function addProduct()
            {
                const product_id = $("#product_id").val().trim();
                const name = $("#name").val().trim();
                const price = parseFloat($("#price").val().trim());
                // const reeferFee = parseFloat($("#reefer_fee").val().trim());

                if (!name || isNaN(price) || isNaN(product_id)) {
                    alert("All fields are required.");
                    return;
                }

                // Check for duplicate product names
                const isDuplicate = selectedProducts.some(product => product.name === name);
                if (isDuplicate) {
                    alert("This product is already added.");
                    return;
                }

                // Add product to the array
                selectedProducts.push({
                    product_id: product_id,
                    name: name,
                    price: price,
                    // reefer_fee: reeferFee,
                });

                // Update the table and reset the form
                updateTable();
                $("#product_id").val("");
                $("#name").val("");
                $("#price").val("");
                // $("#reefer_fee").val("");
            };

            $("#product_id, #name, #price").on("keydown", function (e) {
                if (e.keyCode === 13) {
                    e.preventDefault();
                    addProduct();
                }
            });

            $("#add-product").click(function (e) {
                e.preventDefault();
                addProduct();
            });
            // Function to update the table with selected products
            function updateTable() {
                let tableBody = $("#ordered-products tbody");
                tableBody.empty();

                selectedProducts.forEach((product, index) => {
                    let row = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${product.name}</td>
                    <td>${product.price}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-product" data-index="${index}">
                            Close
                        </button>
                    </td>
                </tr>`;
                    tableBody.append(row);
                });

                // Update summary calculations
                updateSummary();
            }

            // Function to update the summary (subtotal, refer fee total, discount, final amount)
            function updateSummary() {
                let subtotal = 0;
                let referFeeTotal = 0;

                selectedProducts.forEach(product => {
                    subtotal += product.price;
                    // referFeeTotal += product.reefer_fee;
                });

                // Update Subtotal and Refer Fee Total
                $("#subtotal").text(subtotal.toFixed(2));
                // $("#refer-fee-total").text(referFeeTotal.toFixed(2));

                // Get discount values (percent or taka)
                const discountPercent = parseFloat($("#discount-percent").val()) || 0;
                const discountTaka = parseFloat($("#discount-taka").val()) || 0;
                let discountAmount = 0;

                if (discountPercent > 0) {
                    discountAmount = (subtotal * discountPercent) / 100;
                } else if (discountTaka > 0) {
                    discountAmount = discountTaka;
                }

                // Update discount amount in the table
                $("#discount-amount").text(discountAmount.toFixed(2));

                // Get Paid Amount
                const paidAmount = parseFloat($("#paid-amount").val()) || 0;
                const dueAmount = subtotal  - discountAmount - paidAmount;

                const finalAmount = subtotal - discountAmount;

                // Update Due and Final Amount
                $("#due-amount").text(dueAmount.toFixed(2));
                $("#final-amount").text(finalAmount.toFixed(2));
                $("#final-refer").text(referFeeTotal.toFixed(2));
            }

            // Handle removal of product from the table
            $(document).on("click", ".remove-product", function () {
                const productIndex = $(this).data("index");
                selectedProducts.splice(productIndex, 1); // Remove product

                // Update table after removal
                updateTable();
            });

            // Handle input changes for discount and paid amount
            $("#discount-percent, #discount-taka, #paid-amount").on("input", function () {
                updateSummary();
            });

            $(document).on("click", ".btn-store-data", function (e) {
                e.preventDefault();
                const $button = $(this); // Reference the clicked button
                $button.prop("disabled", true);
                setTimeout(() => $button.prop("disabled", false), 3000);
                // Collect data
                const products = selectedProducts.map(product => ({
                    product_id: product.product_id, // Assuming `value` holds the product ID
                    price: product.price,
                    // ref_amount: product.reefer_fee
                }));

                const customerDetails = {
                    invoice_number: $("#invoice_number").val(),
                    patient_name: $("#patient_name").val(),
                    patient_age_year: $("#patient_age_year").val(),
                    patient_phone: $("#patient_phone").val(),
                    patient_blood_group: $("#patient_blood_group").val(),
                    patient_address: $("#patient_address").val(),
                    patient_gender: $("#patient_gender").val(),
                    dr_refer_id: $("#dr_refer_id").val(),
                    dr_refer_name: $("#dr_refer_name").val(),
                    refer_id: $("#refer_id").val(),
                    creation_date: $("#creation_date").val(),
                    delivery_at: $("#delivery_at").val(),
                    discount_by: $("#discount-by").val(),
                };

                // const refs = {
                //     dr_name: $("#dr_refer_id").val(),
                //     referred_by: $("#refer_id").val(),
                // };

                const paymentDetails = {
                    paid_amount: parseFloat($("#paid-amount").val()) || 0,
                    discount_amount: parseFloat($("#discount-amount").text()) || 0,
                    total_amount: parseFloat($("#final-amount").text()) || 0,
                    refer_fee_total: parseFloat($("#final-refer").text()) || 0,
                };

                // Validate before submission
                if (!products.length || !customerDetails.patient_name|| !customerDetails.patient_phone || !customerDetails.patient_age_year) {
                    alert("Please ensure all required fields are filled out.");
                    return;
                } if (!customerDetails.dr_refer_name || !customerDetails.refer_id) {
                    alert("Doctor and Refer Field Required !");
                    return;
                }

                // Submit data via AJAX
                $.ajax({
                    url: "{{ route('admin.invoices.store') }}",
                    type: "POST",
                    contentType: "application/json",
                    data: JSON.stringify({
                        products,
                        customerDetails,
                        paymentDetails,
                    }),
                    success: function (response) {

                        // alert("Order saved successfully!");
                        if(response.invoice_id){
                        const invoiceId = response.invoice_id;
                        const customer_name = response.customer_name;
                        const invoiceUrl = `{{ route('admin.invoices.pdf-preview', ':id') }}`.replace(':id', invoiceId);
                        localStorage.removeItem('invoiceMessage');
                        // Save the message in localStorage
                        localStorage.setItem('invoiceMessage', `<p class="alert alert-success text-center">"${customer_name}" new invoice is available: <a class="btn btn-danger" href="${invoiceUrl}" target="_blank">Click <i class="fas fa-print"></i></a></p>`);

                        location.reload();
                        }
                        else{
                            localStorage.setItem('invoiceMessage', `<p class="alert alert-danger text-center">Something Went wrong try again</p>`);

                        }
                    },
                    error: function (error) {
                        alert("Failed to save the order. Please try again.");
                    }
                });
            });

        });

        $(document).ready(function () {
            const message = localStorage.getItem('invoiceMessage');
            if (message) {
                $('.success-pdf').prepend(`<div>${message}</div>`);
                localStorage.removeItem('invoiceMessage'); // Clear the message after displaying it
            }
        });

    </script>
@endpush
