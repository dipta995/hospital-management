@extends('backend.layouts.master')
@section('title')
    Create New {{ $pageHeader['title'] }}
@endsection
@push('styles')
    @include('backend.layouts.partials.invoice-styles')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
        #ordered-products tfoot td { vertical-align: middle; }
        #discount-percent-row, #discount-taka-row { display: contents; }
    </style>
@endpush

@section('admin-content')
    <div class="inv-page container-fluid py-3">
        <div class="inv-hero">
            <div class="inv-hero-inner">
                <div class="inv-hero-left">
                    <div class="inv-hero-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                    <div>
                        <h1 class="inv-hero-title">Direct Invoice</h1>
                        <p class="inv-hero-sub">Invoice #{{ $invoice_number }} — New walk-in patient</p>
                    </div>
                </div>
                <div class="inv-hero-actions">
                    <a href="{{ route($pageHeader['index_route']) }}" class="inv-btn-glass"><i class="fas fa-arrow-left"></i> Back</a>
                </div>
            </div>
        </div>
        <span class="success-pdf"></span>
        @include('backend.layouts.partials.message')
        <div class="inv-steps">
            <div class="inv-step active"><span class="inv-step-num">1</span> Patient</div>
            <div class="inv-step"><span class="inv-step-num">2</span> Tests</div>
            <div class="inv-step"><span class="inv-step-num">3</span> Payment</div>
        </div>
        <div class="inv-form-layout">
            <div class="inv-form-main">
                <fieldset>
                    <input type="hidden" id="customer_id" class="customer_id" value="">
                    <div class="inv-section">
                        <div class="inv-section-head"><i class="fas fa-user-injured"></i> Patient Details</div>
                        <div class="inv-section-body"><div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-default.label required="true" for="invoice_number">Invoice No</x-default.label>
                                                <x-default.input readonly="readonly" name="invoice_number" value="{{ $invoice_number }}" class="form-control" id="invoice_number" type="text"></x-default.input>
                                                <x-default.input-error name="invoice_number"></x-default.input-error>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-default.label required="true" for="patient_phone">Phone  <span class="text-danger">*</span></x-default.label>
                                                <x-default.input name="patient_phone" class="form-control" value=""  id="patient_phone" type="text" ></x-default.input>
                                                <x-default.input-error name="patient_phone"></x-default.input-error>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-default.label required="true" for="patient_name">Patient Name <span class="text-danger">*</span></x-default.label>
                                                <x-default.input   name="patient_name"  value="" class="form-control" id="patient_name" type="text"></x-default.input>
                                                <x-default.input-error name="patient_name"></x-default.input-error>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-default.label required="true" for="patient_age_year">Age  <span class="text-danger">*</span></x-default.label>
                                                <x-default.input name="patient_age_year" value="" class="form-control" id="patient_age_year" type="text" ></x-default.input>
                                                <x-default.input-error name="patient_age_year"></x-default.input-error>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-default.label required="true" for="patient_blood_group">Blood Group</x-default.label>
                                                <x-default.input name="patient_blood_group" class="form-control" value=""  id="patient_blood_group" type="text" ></x-default.input>
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
                                                <x-default.input name="patient_address" class="form-control"  id="patient_address" type="text" ></x-default.input>
                                                <x-default.input-error name="patient_address"></x-default.input-error>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-default.label required="true" for="dr_refer_name">
                                                    Dr Name
                                                    <button type="button" class="btn btn-sm btn-outline-primary ms-1 openDoctorModal" data-target-input="dr_refer_name">Add Doctor</button>
                                                </x-default.label>
                                                <x-default.input name="dr_refer_name" class="form-control" required id="dr_refer_name" type="text" ></x-default.input>
                                                <x-default.input-error name="dr_refer_name"></x-default.input-error>
                                                <input type="hidden" name="dr_refer_id" id="dr_refer_id">

                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <x-default.label required="true" for="refer_name">Referred By<button type="button" class="btn btn-sm btn-outline-primary ms-1 openOtherModal" data-target-input="refer_name">Add PC</button>
                                                </x-default.label>
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
                    </div>
                </div>

                <div class="inv-section">
                    <div class="inv-section-head"><i class="fas fa-flask"></i> Add Tests</div>
                    <div class="inv-section-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="name">Test Name <span class="text-danger">*</span></label>
                                <div class="inv-test-search">
                                    <i class="fas fa-search"></i>
                                    <x-default.input name="name" class="form-control" id="name" type="text" placeholder="Search test..."></x-default.input>
                                </div>
                                <input type="hidden" name="product_id" id="product_id">
                            </div>
                            <div class="col-md-4">
                                <label for="price">Price</label>
                                <x-default.input name="price" class="form-control" id="price" type="number"></x-default.input>
                            </div>
                            <div class="col-12 text-end">
                                <button type="button" class="inv-add-test-btn" id="add-product"><i class="fas fa-plus"></i> Add to Invoice</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="inv-section">
                    <div class="inv-section-head"><i class="fas fa-receipt"></i> Invoice Line Items & Payment</div>
                    <div class="inv-section-body">
                        <div class="inv-table-wrap">
                            <div class="table-responsive">
                                <table id="ordered-products" class="table inv-table table-bordered mb-0">
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
                            </div>
                        </div>
                    </div>
                </div>
                </fieldset>
            </div>

            <div class="inv-summary-sticky">
                <div class="inv-summary-card">
                    <h5><i class="fas fa-calculator"></i> Payment Summary</h5>
                    <div class="inv-summary-body">
                    <div class="inv-summary-row"><span>Tests</span><span class="val" id="side-test-count">0</span></div>
                    <div class="inv-summary-row"><span>Subtotal</span><span class="val">৳ <span id="side-subtotal">0.00</span></span></div>
                    <div class="inv-summary-row"><span>Discount</span><span class="val">− ৳ <span id="side-discount">0.00</span></span></div>
                    <div class="inv-summary-row paid"><span>Paid</span><span class="val">৳ <span id="side-paid">0.00</span></span></div>
                    <div class="inv-summary-row due"><span>Due</span><span class="val">৳ <span id="side-due">0.00</span></span></div>
                    <div class="inv-summary-row total"><span>Final</span><span class="val">৳ <span id="side-final">0.00</span></span></div>
                    </div>
                    <button type="button" class="inv-summary-submit btn-store-data"><i class="fas fa-check-circle me-1"></i> Create Invoice</button>
                </div>
            </div>
        </div>

        <div class="modal fade inv-modal" id="doctorAddModal" tabindex="-1">
            <div class="modal-dialog">
                <form id="doctorAddForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Doctor</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Doctor Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" name="phone" >
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Parent(%)</label>
                                <input type="number" class="form-control" name="percent" value="0" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Designation</label>
                                <input type="text" class="form-control" name="designation" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Type</label>
                                <select class="form-control" name="type" required>
                                    <option value="{{ \App\Models\Reefer::$typeArray[0] }}">{{ \App\Models\Reefer::$typeArray[0] }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save Doctor</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade inv-modal" id="otherAddModal" tabindex="-1">
            <div class="modal-dialog">
                <form id="otherAddForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New PC</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">PC Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" name="phone" >
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Parent(%)</label>
                                <input type="number" class="form-control" name="percent" value="0" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Type</label>
                                <select class="form-control" name="type" required>
                                    <option value="{{ \App\Models\Reefer::$typeArray[1] }}">{{ \App\Models\Reefer::$typeArray[1] }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save PC</button>
                        </div>
                    </div>
                </form>
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
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // Autocomplete for patient phone
            $('#patient_phone').autocomplete({
                minLength: 4,
                source: function(request, response) {
                    if(request.term.length < 4) return response([]);
                    $.ajax({
                        url: '/admin/search-phone', // your API route
                        type: 'GET',
                        dataType: 'json',
                        data: { query: request.term },
                        success: function(data) {
                            response(data.map(item => ({
                                label: `${item.name} (${item.phone})`,
                                value: item.phone,
                                ...item // include all other fields like name, age, address
                            })));
                        },
                        error: function() {
                            response([]);
                        }
                    });
                },
                select: function(event, ui) {
                    // Fill all fields if suggestion is selected
                    $('#patient_phone').val(ui.item.phone);
                    $('#customer_id').val(ui.item.userId);
                    $('#patient_name').val(ui.item.name || '');
                    $('#patient_age_year').val(ui.item.age || '');
                    $('#patient_address').val(ui.item.address || '');
                    $('#patient_gender').val(ui.item.gender || 'Male');
                    $('#patient_blood_group').val(ui.item.blood_group || '');
                    // If needed, add hidden IDs
                    // $('#patient_id').val(ui.item.id || '');

                    return false; // prevent default value overwrite
                },
                focus: function(event, ui) {
                    // Show suggestion label on hover
                    $('#patient_phone').val(ui.item.phone);
                    return false;
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
                $("#side-test-count").text(selectedProducts.length);
                $("#side-subtotal, #subtotal").text(subtotal.toFixed(2));
                $("#side-discount").text(discountAmount.toFixed(2));
                $("#side-paid").text(paidAmount.toFixed(2));
                $("#side-due").text(dueAmount.toFixed(2));
                $("#side-final").text(finalAmount.toFixed(2));
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
                    for: $("#customer_id").val(),
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



        $(document).ready(function () {
            const message = localStorage.getItem('invoiceMessage');
            if (message) {
                $('.success-pdf').prepend(`<div>${message}</div>`);
                localStorage.removeItem('invoiceMessage'); // Clear the message after displaying it
            }
        });
            $(document).on('click', '.openDoctorModal', function() {
                let target = $(this).attr('data-target-input');
                $('#doctorAddModal').data('target-input', target).modal('show');
            }); $(document).on('click', '.openOtherModal', function() {
                let target = $(this).attr('data-target-input');
                $('#otherAddModal').data('target-input', target).modal('show');
            });

            $('#doctorAddForm').on('submit', function(e) {
                e.preventDefault();

                let formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('admin.reefers.store') }}",
                    type: "POST",
                    data: formData,
                    success: function(response) {

                        let targetInput = $('#doctorAddModal').data('target-input');

                        $("#" + targetInput).val(response.name);  // insert name
                        $("#"+targetInput.replace("_name","_id")).val(response.id); // insert id

                        $('#doctorAddModal').modal('hide');
                        $('#doctorAddForm')[0].reset();
                    }
                });
            });
            $('#otherAddForm').on('submit', function(e) {
                e.preventDefault();

                let formData = $(this).serialize()
                $.ajax({
                    url: "{{ route('admin.reefers.store') }}",
                    type: "POST",
                    data: formData,
                    success: function(response) {

                        let targetInput = $('#otherAddModal').data('target-input');

                        $("#" + targetInput).val(response.name);  // insert name
                        $("#"+targetInput.replace("_name","_id")).val(response.id); // insert id

                        $('#otherAddModal').modal('hide');
                        $('#otherAddForm')[0].reset();
                    }
                });
            });
        });
    </script>
@endpush
