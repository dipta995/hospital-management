@extends('backend.layouts.master')
@section('title')
    Edit {{ $pageHeader['title'] }}
@endsection
@push('styles')
    @include('backend.layouts.partials.invoice-styles')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
        #ordered-products tfoot td { vertical-align: middle; }
        #ordered-products tfoot td input,
        #ordered-products tfoot td span { width: 100%; max-width: 100%; }
    </style>
@endpush

@section('admin-content')
    @php
        $paidTotal = (float) ($edited->paid_amount_sum_paid_amount ?? 0);
        $dueTotal = (float) $edited->total_amount - $paidTotal;
        $dueClass = $dueTotal > 0.009 ? 'is-due' : ($dueTotal < -0.009 ? 'is-overpaid' : 'is-paid');
    @endphp
    <div class="inv-page container-fluid py-3">
        <div class="inv-hero">
            <div class="inv-hero-inner">
                <div class="inv-hero-left">
                    <div class="inv-hero-icon"><i class="fas fa-file-pen"></i></div>
                    <div>
                        <h1 class="inv-hero-title">Edit Invoice</h1>
                        <p class="inv-hero-sub">#{{ $edited->invoice_number }} — {{ $edited->patient_name }}</p>
                    </div>
                </div>
                <div class="inv-hero-actions">
                    <a href="{{ route($pageHeader['index_route']) }}" class="inv-btn-glass"><i class="fas fa-arrow-left"></i> Back</a>
                </div>
            </div>
        </div>
        @include('backend.layouts.partials.message')

        <div class="inv-pay-stat-grid">
            <div class="inv-pay-stat">
                <div class="inv-pay-stat-label">Invoice Total</div>
                <div class="inv-pay-stat-value">৳ {{ number_format($edited->total_amount, 2) }}</div>
            </div>
            <div class="inv-pay-stat">
                <div class="inv-pay-stat-label">Paid Amount</div>
                <div class="inv-pay-stat-value is-paid" id="hero-paid">৳ {{ number_format($paidTotal, 2) }}</div>
            </div>
            <div class="inv-pay-stat">
                <div class="inv-pay-stat-label">{{ $dueTotal < -0.009 ? 'Overpaid' : 'Due Amount' }}</div>
                <div class="inv-pay-stat-value {{ $dueClass }}" id="hero-due">৳ {{ number_format($dueTotal, 2) }}</div>
            </div>
        </div>

        <div class="inv-form-layout">
            <div class="inv-form-main">
                <fieldset>
                    <div class="inv-section">
                        <div class="inv-section-head"><i class="fas fa-user-injured"></i> Patient Details</div>
                        <div class="inv-section-body"><div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <x-default.label required="true" for="invoice_number">Invoice No</x-default.label>
                                            <x-default.input readonly="readonly" name="invoice_number" value="{{ $edited->invoice_number }}" class="form-control" id="invoice_number" type="text"></x-default.input>
                                            <x-default.input-error name="invoice_number"></x-default.input-error>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <x-default.label required="true" for="patient_name">Patient Name</x-default.label>
                                            <x-default.input name="patient_name" value="{{ $edited->patient_name }}" class="form-control" id="patient_name" type="text"></x-default.input>
                                            <x-default.input-error name="patient_name"></x-default.input-error>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <x-default.label required="true" for="patient_age_year">Age</x-default.label>
                                            <x-default.input name="patient_age_year" value="{{ $edited->patient_age_year }}" class="form-control" id="patient_age_year" type="text" ></x-default.input>
                                            <x-default.input-error name="patient_age_year"></x-default.input-error>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <x-default.label required="true" for="patient_phone">Phone</x-default.label>
                                            <x-default.input name="patient_phone" value="{{ $edited->patient_phone }}" class="form-control" id="patient_phone" type="text" ></x-default.input>
                                            <x-default.input-error name="patient_phone"></x-default.input-error>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-default.label required="true" for="patient_blood_group">Blood Group</x-default.label>
                                            <x-default.input name="patient_blood_group" value="{{ $edited->patient_blood_group }}" class="form-control" id="patient_blood_group" type="text" ></x-default.input>
                                            <x-default.input-error name="patient_blood_group"></x-default.input-error>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <x-default.label required="true" for="patient_gender">Gender</x-default.label>
                                            <select class="form-control" name="patient_gender" id="patient_gender">
                                                <option value="">Choose</option>
                                                <option @selected($edited->patient_gender == 'Male') value="Male">Male</option>
                                                <option @selected($edited->patient_gender == 'Female') value="Female">Female</option>
                                                <option @selected($edited->patient_gender == 'Other') value="Other">Other</option>
                                            </select>
                                            <x-default.input-error name="patient_gender"></x-default.input-error>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <x-default.label required="true" for="patient_address">Address</x-default.label>
                                            <x-default.input name="patient_address" value="{{ $edited->patient_address }}" class="form-control" id="patient_address" type="text" ></x-default.input>
                                            <x-default.input-error name="patient_address"></x-default.input-error>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <x-default.label required="true" for="dr_refer_name">Dr Name</x-default.label>
                                            <x-default.input name="dr_refer_name"  value="{{ $edited->reeferDr->name ?? $edited->dr_name }}" class="form-control" id="dr_refer_name" type="text" ></x-default.input>
                                            <x-default.input-error name="dr_refer_name"></x-default.input-error>
                                            <input type="hidden" value="{{ $edited->dr_refer_id ?? null }}" name="dr_refer_id" id="dr_refer_id">

                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <x-default.label required="true" for="refer_name">Reffered By</x-default.label>
                                            <x-default.input name="refer_name"  value="{{ $edited->reeferBy->name ?? null}}"  class="form-control" id="refer_name" type="text" ></x-default.input>
                                            <x-default.input-error name="refer_name"></x-default.input-error>
                                            <input type="hidden" name="refer_id" value="{{ $edited->refer_id ?? null}}"  id="refer_id">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <x-default.label required="true" for="delivery_at">Delivery At</x-default.label>
                                                <x-default.input name="delivery_at" class="form-control" id="delivery-at" value="{{ \Carbon\Carbon::parse($edited->delivery_at)->format('Y-m-d\TH:i') }}" type="datetime-local"> ></x-default.input>
                                                <x-default.input-error name="delivery_at"></x-default.input-error>
                                            </div>
                                        </div>
                                    </div>
                        </div>
                    </div>
                </div>

                <div class="inv-section">
                    <div class="inv-section-head"><i class="fas fa-flask"></i> Add Tests</div>
                    <div class="inv-section-body"><div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <x-default.label required="true" for="name">Name</x-default.label>
                                            <x-default.input name="name" class="form-control" id="name" type="text"></x-default.input>
                                            <x-default.input-error name="name"></x-default.input-error>
                                        </div>
                                        <input type="hidden" name="product_id" id="product_id" >
                                        <input type="hidden" id="product_category_id">
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <x-default.label required="true" for="price">Price</x-default.label>
                                            <x-default.input name="price" class="form-control" id="price" type="number" ></x-default.input>
                                            <x-default.input-error name="price"></x-default.input-error>
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-none">
                                        <div class="form-group">
                                            <x-default.label for="reefer_fee">Refer Fee</x-default.label>
                                            <x-default.input name="reefer_fee" class="form-control" id="reefer_fee" type="number" ></x-default.input>
                                        </div>
                                    </div>
                            <div class="col-12 text-end">
                                <button type="button" class="inv-add-test-btn" id="add-product"><i class="fas fa-plus"></i> Add Test</button>
                            </div>
                        </div></div>
                </div>

                <div class="inv-section">
                    <div class="inv-section-head"><i class="fas fa-receipt"></i> Invoice Details</div>
                    <div class="inv-section-body">
                        <div class="inv-table-wrap inv-form-table-wrap"><div class="table-responsive">
                                <table id="ordered-products" class="table inv-table table-bordered mb-0">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Test Name</th>
                                        <th>Price</th>
                                        <th>Refer Fee</th>
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
                                        <td id="refer-fee-total" >0</td>
                                        <td></td>

                                    </tr>

                                    <!-- Discount Choose section -->
                                    <tr>
                                        <td colspan="2"><strong>Discount Choose</strong></td>
                                        <td colspan="3">
                                            <label><input type="radio" id="discount-choose" name="discount-type" value="percent"> Percent</label>
                                            <label><input type="radio" id="discount-choose" name="discount-type" checked value="taka"> Taka</label>
                                        </td>
                                    </tr>

                                    <!-- Discount Percentage Section -->
                                    <tr id="discount-percent-row" style="display: none;">
                                        <td colspan="2"><strong>Discount (%)</strong></td>
                                        <td><input type="number" id="discount-percent" class="form-control" value="0"></td>
                                        <td colspan="2"><strong>Discount Amount:</strong> <span id="discount-amount">0</span></td>
                                    </tr>

                                    <!-- Discount Taka Section -->
                                    <tr id="discount-taka-row" >
                                        <td colspan="2"><strong>Discount (TK)</strong></td>
                                        <td><input type="number"  id="discount-taka" value="{{ $edited->discount_amount }}" class="form-control"></td>
                                        <td colspan="2"><strong>Discount Amount:</strong> <span id="discount-amount-taka">0</span></td>
                                    </tr>

                                    <!-- Discount By Section -->
                                    <tr>
                                        <td colspan="2"><strong>Discount By:</strong></td>
                                        <td colspan="3"><input type="text" id="discount-by" name="discount_by" value="{{ $edited->discount_by }}" class="form-control"></td>
                                    </tr>

                                    <!-- Final Amount Section -->
                                    <tr>
                                        <td colspan="2"><strong>Final Amount</strong></td>
                                        <td colspan="3">
                                            <span id="final-amount">{{ $edited->total_amount }}</span>
                                            (<span id="final-refer">{{ $edited->refer_fee_total }}</span>)
                                        </td>
                                    </tr>
                                    </tfoot>
                                </table></div></div>
                    </div>
                </div>

                <div class="inv-section">
                    <div class="inv-section-head"><i class="fas fa-money-bill-wave"></i> Payment</div>
                    <div class="inv-section-body">
                        <div class="inv-payment-panel">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label" for="paid-amount">Paid Amount (Total)</label>
                                    <input type="number" step="0.01" id="paid-amount"
                                           value="{{ number_format($paidTotal, 2, '.', '') }}"
                                           class="form-control">
                                    <small class="text-muted">Change this to adjust total paid on save.</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Due / Overpaid</label>
                                    <div id="due-display" class="inv-due-display {{ $dueClass }}">৳ {{ number_format($dueTotal, 2) }}</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Final Bill</label>
                                    <div id="final-display" class="inv-due-display is-paid">৳ {{ number_format($edited->total_amount, 2) }}</div>
                                </div>
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
                    <div class="inv-summary-row total"><span>Final</span><span class="val">৳ <span id="side-final">{{ number_format($edited->total_amount, 2) }}</span></span></div>
                    </div>
                    <button type="button" class="inv-summary-submit btn-store-data"><i class="fas fa-save me-1"></i> Update Invoice</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('backend.layouts.partials.invoice-refer-commission')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

    <script>
        $(document).ready(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            let selectedProducts = []; // Holds all products for the invoice

            // Preload data for editing (replace `existingProducts` with actual data from server-side)
            const referCommission = @json($referCommission);
            if (referCommission) {
                window.InvoiceReferCommission.setRefer(referCommission);
            }

            const existingProducts = @json($products);
            if (existingProducts && existingProducts.length) {
                selectedProducts = existingProducts.map(product => ({
                    product_id: product.product_id,
                    name: product.product_name,
                    price: parseFloat(product.price),
                    category_id: product.category_id,
                    reefer_fee: parseFloat(product.reefer_fee),
                }));
                updateTable();
            }

            // Function to configure autocomplete fields
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
                $("#product_id").val(item.productID);
                $("#product_category_id").val(item.category_id || '');
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
                window.InvoiceReferCommission.setRefer(item);
                updateTable();
            });
            // Handle radio button change for discount type (percent or taka)
            $("input[name='discount-type']").on("change", function () {
                const selectedDiscountType = $("input[name='discount-type']:checked").val();
                if (selectedDiscountType === "percent") {
                    // $("#discount-taka").val(0);
                    $("#discount-percent-row").show();
                    $("#discount-taka-row").hide();
                } else if (selectedDiscountType === "taka") {
                    // $("#discount-percent").val(0);
                    $("#discount-percent-row").hide();
                    $("#discount-taka-row").show();
                }
                updateSummary(); // Update summary after selecting discount type
            });

            // Add product to the table on "Add" button click
            $("#add-product").click(function (e) {
                e.preventDefault();

                const product_id = $("#product_id").val().trim();
                const name = $("#name").val().trim();
                const price = parseFloat($("#price").val().trim());
                const category_id = $("#product_category_id").val() ? parseInt($("#product_category_id").val(), 10) : null;

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
                    category_id: category_id,
                });

                updateTable(); // Update the table
                resetProductForm(); // Reset the input form
            });

            // Function to reset the product form fields
            function resetProductForm() {
                $("#product_id").val("");
                $("#product_category_id").val("");
                $("#name").val("");
                $("#price").val("");
                $("#reefer_fee").val("");
            }

            // Function to update the table with selected products
            function updateTable() {
                let tableBody = $("#ordered-products tbody");
                tableBody.empty();

                selectedProducts.forEach((product, index) => {
                    const lineReferFee = window.InvoiceReferCommission.lineAmount(product);
                    let row = `
                <tr>
                    <td data-label="#">${index + 1}</td>
                    <td data-label="Test">${product.name}</td>
                    <td data-label="Price">${product.price}</td>
                    <td data-label="Refer Fee">${lineReferFee.toFixed(2)}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-product" data-index="${index}">
                            Close
                        </button>
                    </td>
                </tr>`;
                    tableBody.append(row);
                });

                updateSummary(); // Recalculate summary values
            }

            // Function to update the summary (subtotal, refer fee total, discount, final amount)
            function updateSummary() {
                let subtotal = 0;

                selectedProducts.forEach(product => {
                    subtotal += product.price;
                });

                $("#subtotal").text(subtotal.toFixed(2));

                const discountPercent = parseFloat($("#discount-percent").val()) || 0;
                const discountTaka = parseFloat($("#discount-taka").val()) || 0;
                let discountAmount = 0;

                if (discountPercent > 0) {
                    discountAmount = (subtotal * discountPercent) / 100;
                } else if (discountTaka > 0) {
                    discountAmount = discountTaka;
                }

                const referFeeTotal = window.InvoiceReferCommission.calculate(
                    selectedProducts,
                    discountAmount,
                    subtotal
                );

                $("#discount-amount").text(discountAmount.toFixed(2));

                const paidAmount = parseFloat($("#paid-amount").val()) || 0;
                const finalAmount = subtotal - discountAmount;
                const dueAmount = finalAmount - paidAmount;

                $("#final-amount").text(finalAmount.toFixed(2));
                $("#final-refer").text(referFeeTotal.toFixed(2));
                $("#side-test-count").text(selectedProducts.length);
                $("#side-subtotal, #subtotal").text(subtotal.toFixed(2));
                $("#side-discount").text(discountAmount.toFixed(2));
                $("#side-paid").text(paidAmount.toFixed(2));
                $("#hero-paid").text('৳ ' + paidAmount.toFixed(2));
                $("#side-due").text(dueAmount.toFixed(2));
                $("#hero-due").text('৳ ' + dueAmount.toFixed(2));
                $("#side-final").text(finalAmount.toFixed(2));
                $("#final-display").text('৳ ' + finalAmount.toFixed(2));

                const $dueDisplay = $("#due-display");
                $dueDisplay.text('৳ ' + dueAmount.toFixed(2));
                $dueDisplay.removeClass('is-due is-paid is-overpaid');
                $("#hero-due").removeClass('is-due is-paid is-overpaid');
                $("#side-due").removeClass('is-overpaid');

                if (dueAmount > 0.009) {
                    $dueDisplay.addClass('is-due');
                    $("#hero-due").addClass('is-due');
                } else if (dueAmount < -0.009) {
                    $dueDisplay.addClass('is-overpaid');
                    $("#hero-due, #side-due").addClass('is-overpaid');
                } else {
                    $dueDisplay.addClass('is-paid');
                    $("#hero-due").addClass('is-paid');
                }
            }

            // Handle removal of products from the table
            $(document).on("click", ".remove-product", function () {
                const productIndex = $(this).data("index");
                selectedProducts.splice(productIndex, 1); // Remove the product
                updateTable(); // Refresh the table
            });

            // Handle input changes for discount and paid amount
            $("#discount-percent, #discount-taka, #paid-amount").on("input", function () {
                updateSummary();
            });

            // AJAX Submission
            $(document).on("click", ".btn-store-data", function (e) {
                e.preventDefault();

                const products = selectedProducts.map(product => ({
                    product_id: product.product_id,
                    price: product.price,
                    ref_amount: window.InvoiceReferCommission.lineAmount(product),
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
                    delivery_at: $("#delivery-at").val(),
                };

                const paymentDetails = {
                    discount_by: $("#discount-by").val(),
                    paid_amount: parseFloat($("#paid-amount").val()) || 0,
                    discount_amount: parseFloat($("#discount-amount").text()) || 0,
                    total_amount: parseFloat($("#final-amount").text()) || 0,
                    refer_fee_total: parseFloat($("#final-refer").text()) || 0,
                };

                if (!products.length || !customerDetails.patient_name ) {
                    alert("Please ensure all required fields are filled out.");
                    return;
                }

                $.ajax({
                    url: "{{ route('admin.invoices.update',$edited->id) }}",
                    type: "POST",
                    contentType: "application/json",
                    data: JSON.stringify({ _method: "PUT", products, customerDetails, paymentDetails }),
                    success: function () {
                        alert("Invoice updated successfully!");
                        location.reload();
                    },
                    error: function (xhr) {
                        const msg = xhr.responseJSON?.message || "Failed to save the order. Please try again.";
                        alert(msg);
                    }
                });
            });

            updateSummary();
        });

    </script>
@endpush
