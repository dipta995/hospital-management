@extends('backend.layouts.master')
@section('title')
    Create New {{ $pageHeader['title'] }}
@endsection
@push('styles')
    @include('backend.layouts.partials.crud-styles')
    @include('backend.layouts.partials.pharmacy-styles')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush
@section('admin-content')
<div class="crud-page pharm-page container-fluid py-3">
    @include('backend.layouts.partials.crud-form-hero', [
        'formTitle' => 'Pharmacy POS — New Sale',
        'formSubtitle' => 'Search customer & products, complete payment',
        'formIcon' => 'fa-cash-register',
    ])

    <div class="d-flex justify-content-end mb-3 gap-2">
        <button type="button" class="btn btn-outline-primary btn-sm d-none" id="print-invoice-top"><i class="fas fa-print"></i> Invoice</button>
    </div>

    @include('backend.layouts.partials.message')

    <div class="pharm-pos-grid">
        <div>
            <div class="pharm-pos-panel mb-3">
                <div class="pharm-pos-panel-head"><i class="fas fa-user"></i> Customer</div>
                <div class="pharm-pos-panel-body">
                    <div class="row g-2">
                        <div class="col-md-5 position-relative">
                            <label class="form-label">Search Name / Phone</label>
                            <input type="text" id="customer_search" class="form-control" placeholder="Type to search...">
                            <ul class="list-group pharm-suggestion" id="customer_suggestion"></ul>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#customerModal"><i class="fas fa-user-plus"></i> New</button>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Selected Customer</label>
                            <input type="text" id="customer_name" class="form-control bg-light" readonly placeholder="No customer selected">
                            <input type="hidden" id="customer_id">
                        </div>
                    </div>
                </div>
            </div>

            <div class="pharm-pos-panel mb-3">
                <div class="pharm-pos-panel-head"><i class="fas fa-user-md"></i> Referring Doctor <small class="text-muted">(optional)</small></div>
                <div class="pharm-pos-panel-body">
                    <label for="dr_refer_name" class="form-label">Doctor
                        <button type="button" class="btn btn-sm btn-outline-primary py-0 openDoctorModal" data-target-input="dr_refer_name">+ Add</button>
                    </label>
                    <input type="text" id="dr_refer_name" class="form-control" placeholder="Search doctor name">
                    <input type="hidden" id="dr_refer_id">
                </div>
            </div>

            <div class="pharm-pos-panel mb-3">
                <div class="pharm-pos-panel-head"><i class="fas fa-pills"></i> Add Product</div>
                <div class="pharm-pos-panel-body">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-5">
                            <label for="product_name" class="form-label">Product</label>
                            <input type="text" id="product_name" class="form-control" placeholder="Name / generic / barcode">
                            <input type="hidden" id="pharmacy_product_id">
                        </div>
                        <div class="col-md-2">
                            <label for="product_qty" class="form-label">Qty <small class="text-muted">Stock: <span id="current_stock">-</span></small></label>
                            <input type="number" id="product_qty" class="form-control" value="1" min="1">
                        </div>
                        <div class="col-md-2">
                            <label for="product_price" class="form-label">Unit Price</label>
                            <input type="number" id="product_price" class="form-control" step="0.01">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-success w-100" id="add-product"><i class="fas fa-plus"></i> Add to Cart</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pharm-pos-panel">
                <div class="pharm-pos-panel-head"><i class="fas fa-shopping-cart"></i> Cart</div>
                <div class="table-responsive">
                    <table class="table pharm-cart-table mb-0" id="sale-products">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Disc.</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="pharm-pos-panel sticky-top" style="top: 80px;">
            <div class="pharm-pos-panel-head"><i class="fas fa-receipt"></i> Payment Summary</div>
            <div class="pharm-pos-panel-body">
                <div class="mb-3">
                    <label class="form-label">Sale Date</label>
                    <input type="date" id="sale_date" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="pharm-summary-row"><span>Subtotal</span><strong>৳ <span id="subtotal_display">0.00</span></strong></div>
                <input type="hidden" id="subtotal">
                <div class="mb-2 mt-2">
                    <label class="form-label">Invoice Discount</label>
                    <input type="number" id="discount_amount" class="form-control" value="0" step="0.01">
                </div>
                <div class="pharm-summary-row total"><span>Grand Total</span><span>৳ <span id="total_display">0.00</span></span></div>
                <input type="hidden" id="total_amount" readonly>
                <div class="mb-2 mt-3">
                    <label class="form-label">Paid Amount</label>
                    <input type="number" id="paid_amount" class="form-control" value="0" step="0.01">
                </div>
                <div class="pharm-summary-row"><span>Due</span><strong class="text-danger">৳ <span id="due_display">0.00</span></strong></div>
                <input type="hidden" id="due_amount" readonly>
                <div class="mb-2 mt-2">
                    <label class="form-label">Payment Method</label>
                    <select id="payment_method" class="form-select">
                        <option value="Cash">Cash</option>
                        <option value="Bkash">Bkash</option>
                        <option value="Nagad">Nagad</option>
                        <option value="Card">Card</option>
                        <option value="Bank">Bank</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Note</label>
                    <input type="text" id="note" class="form-control" placeholder="Optional note">
                </div>
                <button type="button" class="btn btn-crud-submit w-100 btn-lg" id="submit-sale"><i class="fas fa-check-circle"></i> Complete Sale</button>
                <button type="button" class="btn btn-outline-primary w-100 mt-2 d-none" id="print-invoice-bottom"><i class="fas fa-print"></i> Print Invoice</button>
            </div>
        </div>
    </div>
</div>

<!-- Customer Modal -->
<div class="modal fade crud-modal" id="customerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label>Name</label>
                    <input type="text" id="modal_name" class="form-control">
                </div>
                <div class="mb-2">
                    <label>Phone</label>
                    <input type="text" id="modal_phone" class="form-control">
                </div>
                <div class="mb-2">
                    <label>Age</label>
                    <input type="number" id="modal_age" class="form-control">
                </div>
                <div class="mb-2">
                    <label>Gender</label>
                    <select id="modal_gender" class="form-control">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label>Blood Group</label>
                    <input type="text" id="modal_blood_group" class="form-control">
                </div>
                <div class="mb-2">
                    <label>Address</label>
                    <input type="text" id="modal_address" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveCustomer">Save</button>
            </div>
        </div>
    </div>
</div>
<!-- Doctor Add Modal -->
<div class="modal fade" id="doctorAddModal" tabindex="-1">
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
                        <input type="text" class="form-control" name="phone">
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

<!-- Other (PC) Add Modal -->
<div class="modal fade" id="otherAddModal" tabindex="-1">
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
                        <input type="text" class="form-control" name="phone">
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
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script>
    $(document).ready(function () {
        let selectedItems = [];
        let currentProductStock = null;

        function configureAutocomplete(fieldId, sourceUrl, onSelectCallback) {
            $('#' + fieldId).autocomplete({
                source: function (request, response) {
                    if (request.term.trim() === '') {
                        response([]);
                        return;
                    }
                    $.ajax({
                        url: sourceUrl,
                        type: 'GET',
                        data: { query: request.term },
                        success: function (data) {
                            response($.map(data, function (item) {
                                var stockLabel = typeof item.current_stock !== 'undefined' ? ' [Stock: ' + item.current_stock + ']' : '';
                                return {
                                    label: item.name + (item.generic_name ? ' (' + item.generic_name + ')' : '') + stockLabel,
                                    value: item.name,
                                    item: item
                                };
                            }));
                        }
                    });
                },
                minLength: 1,
                select: function (event, ui) {
                    if (onSelectCallback) {
                        onSelectCallback(ui.item.item);
                    }
                }
            });
        }

        function recalcTotals() {
            let subtotal = 0;
            selectedItems.forEach(function (it) {
                subtotal += parseFloat(it.total_amount) || 0;
            });
            $('#subtotal').val(subtotal.toFixed(2));
            $('#subtotal_display').text(subtotal.toFixed(2));
            let invDisc = parseFloat($('#discount_amount').val()) || 0;
            let grand = subtotal - invDisc;
            $('#total_amount').val(grand.toFixed(2));
            $('#total_display').text(grand.toFixed(2));
            let paid = parseFloat($('#paid_amount').val()) || 0;
            let due = grand - paid;
            $('#due_amount').val(due.toFixed(2));
            $('#due_display').text(due.toFixed(2));
        }

        function renderTable() {
            let tbody = $('#sale-products tbody');
            tbody.empty();
            selectedItems.forEach(function (it, index) {
                let row = '<tr data-index="' + index + '">' +
                    '<td>' + (index + 1) + '</td>' +
                    '<td>' + it.name + '</td>' +
                    '<td><input type="number" class="form-control row-qty" value="' + it.quantity + '" min="1"></td>' +
                    '<td><input type="number" class="form-control row-price" value="' + it.unit_price + '" step="0.01"></td>' +
                    '<td><input type="number" class="form-control row-discount" value="' + it.discount_amount + '" step="0.01"></td>' +
                    '<td class="row-total">' + parseFloat(it.total_amount).toFixed(2) + '</td>' +
                    '<td><button type="button" class="btn btn-danger btn-sm remove-item">X</button></td>' +
                    '</tr>';
                tbody.append(row);
            });
            recalcTotals();
        }

        function recalcItem(index) {
            let it = selectedItems[index];
            if (!it) return;
            it.quantity = parseFloat(it.quantity) || 0;
            it.unit_price = parseFloat(it.unit_price) || 0;
            it.discount_amount = parseFloat(it.discount_amount) || 0;
            it.total_amount = (it.quantity * it.unit_price) - it.discount_amount;
        }

        configureAutocomplete('product_name', "{{ url('admin/get-pharmacy-products') }}", function (item) {
            $('#pharmacy_product_id').val(item.id);
            $('#product_price').val(item.sell_price);
            if (!$('#product_qty').val()) {
                $('#product_qty').val(1);
            }

            currentProductStock = typeof item.current_stock !== 'undefined' ? item.current_stock : null;
            if (currentProductStock === null) {
                $('#current_stock').text('-');
            } else {
                $('#current_stock').text(currentProductStock);
            }
        });

        configureAutocomplete('dr_refer_name', "{{ url('admin/get-doctors') }}", function (item) {
            $('#dr_refer_id').val(item.referID);
            $('#dr_refer_name').val(item.name);
        });

        $('#add-product').click(function () {
            let pid = $('#pharmacy_product_id').val();
            let name = $('#product_name').val().trim();
            let qty = parseFloat($('#product_qty').val()) || 0;
            let price = parseFloat($('#product_price').val()) || 0;

            if (!pid || !name || qty <= 0 || price <= 0) {
                alert('Select product with quantity and price.');
                return;
            }

            if (currentProductStock !== null && qty > currentProductStock) {
                alert('You cannot sell more than current stock (' + currentProductStock + ').');
                return;
            }

            let dup = selectedItems.find(function (it) { return it.pharmacy_product_id == pid; });
            if (dup) {
                alert('This product is already added, please adjust quantity in the list.');
                return;
            }

            let total = qty * price;
            selectedItems.push({
                pharmacy_product_id: pid,
                name: name,
                quantity: qty,
                unit_price: price,
                max_stock: currentProductStock,
                discount_amount: 0,
                total_amount: total
            });

            $('#pharmacy_product_id').val('');
            $('#product_name').val('');
            $('#product_qty').val(1);
            $('#product_price').val('');
            currentProductStock = null;
            $('#current_stock').text('-');

            renderTable();
        });

        $(document).on('input', '.row-qty, .row-price, .row-discount', function () {
            let tr = $(this).closest('tr');
            let idx = tr.data('index');
            let newQty = parseFloat(tr.find('.row-qty').val()) || 0;

            if (selectedItems[idx].max_stock !== null && newQty > selectedItems[idx].max_stock) {
                alert('You cannot sell more than current stock (' + selectedItems[idx].max_stock + ').');
                tr.find('.row-qty').val(selectedItems[idx].quantity);
                return;
            }

            selectedItems[idx].quantity = newQty;
            selectedItems[idx].unit_price = parseFloat(tr.find('.row-price').val()) || 0;
            selectedItems[idx].discount_amount = parseFloat(tr.find('.row-discount').val()) || 0;
            recalcItem(idx);
            tr.find('.row-total').text(parseFloat(selectedItems[idx].total_amount).toFixed(2));
            recalcTotals();
        });

        $(document).on('click', '.remove-item', function () {
            let idx = $(this).closest('tr').data('index');
            selectedItems.splice(idx, 1);
            renderTable();
        });

        $('#discount_amount, #paid_amount').on('input', function () {
            recalcTotals();
        });

        $('#paid_amount').on('focus', function () {
            if (parseFloat($(this).val()) === 0) {
                $(this).val($('#total_amount').val());
                recalcTotals();
            }
        });
        // Customer search by name/phone
        $('#customer_search').on('keyup', function () {
            let q = $(this).val();
            if (q.length < 2) {
                $('#customer_suggestion').hide();
                return;
            }
            $.get("{{ url('admin/search-phone') }}", {query: q}, function (res) {
                let list = $('#customer_suggestion');
                list.empty();
                if (res.length) {
                    res.forEach(function (u) {
                        list.append('<li class="list-group-item customer-item" data-id="' + u.userId + '" data-name="' + u.name + '">' + u.name + ' (' + u.phone + ')</li>');
                    });
                    list.show();
                } else {
                    list.hide();
                }
            });
        });

        $(document).on('click', '.customer-item', function () {
            $('#customer_id').val($(this).data('id'));
            $('#customer_name').val($(this).data('name'));
            $('#customer_suggestion').hide();
        });

        // Save customer via API
        $('#saveCustomer').click(function () {
            let data = {
                name: $('#modal_name').val(),
                phone: $('#modal_phone').val(),
                age: $('#modal_age').val(),
                gender: $('#modal_gender').val(),
                blood_group: $('#modal_blood_group').val(),
                address: $('#modal_address').val(),
            };
            $.ajax({
                url: "{{ route('admin.users.store.api') }}",
                method: 'POST',
                data: data,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function (res) {
                    if (res.id) {
                        $('#customer_id').val(res.id);
                        $('#customer_name').val(res.customer_name);
                        $('#customerModal').modal('hide');
                    } else {
                        alert('Failed to create customer');
                    }
                },
                error: function () {
                    alert('Failed to create customer');
                }
            });
        });

        function setPrintButtons(url) {
            if (!url) return;
            $('#print-invoice-top, #print-invoice-bottom')
                .removeClass('d-none')
                .data('url', url);
        }

        $(document).on('click', '#print-invoice-top, #print-invoice-bottom', function () {
            const url = $(this).data('url');
            if (url) {
                window.open(url, '_blank');
            }
        });

        // Doctor / Refer modals
        $(document).on('click', '.openDoctorModal', function () {
            let target = $(this).attr('data-target-input');
            $('#doctorAddModal').data('target-input', target).modal('show');
        });

        $(document).on('click', '.openOtherModal', function () {
            let target = $(this).attr('data-target-input');
            $('#otherAddModal').data('target-input', target).modal('show');
        });

        $('#doctorAddForm').on('submit', function (e) {
            e.preventDefault();

            let formData = $(this).serialize();

            $.ajax({
                url: "{{ route('admin.reefers.store.api') }}",
                type: 'POST',
                data: formData,
                success: function (response) {
                    let targetInput = $('#doctorAddModal').data('target-input');

                    $('#' + targetInput).val(response.name);
                    $('#' + targetInput.replace('_name', '_id')).val(response.id);

                    $('#doctorAddModal').modal('hide');
                    $('#doctorAddForm')[0].reset();
                }
            });
        });

        $('#otherAddForm').on('submit', function (e) {
            e.preventDefault();

            let formData = $(this).serialize();

            $.ajax({
                url: "{{ route('admin.reefers.store.api') }}",
                type: 'POST',
                data: formData,
                success: function (response) {
                    let targetInput = $('#otherAddModal').data('target-input');

                    $('#' + targetInput).val(response.name);
                    $('#' + targetInput.replace('_name', '_id')).val(response.id);

                    $('#otherAddModal').modal('hide');
                    $('#otherAddForm')[0].reset();
                }
            });
        });

        // Submit sale
        $('#submit-sale').click(function () {
            if (!$('#customer_id').val()) {
                alert('Please select or add a customer.');
                return;
            }
            if (!selectedItems.length) {
                alert('Please add at least one product.');
                return;
            }

            let items = selectedItems.map(function (it) {
                return {
                    pharmacy_product_id: it.pharmacy_product_id,
                    quantity: it.quantity,
                    unit_price: it.unit_price,
                    discount_amount: it.discount_amount,
                    total_amount: it.total_amount,
                };
            });

            let payload = {
                customer_id: $('#customer_id').val(),
                dr_refer_id: $('#dr_refer_id').val(),
                sale_date: $('#sale_date').val(),
                total_amount: $('#total_amount').val(),
                discount_amount: $('#discount_amount').val(),
                paid_amount: $('#paid_amount').val(),
                due_amount: $('#due_amount').val(),
                payment_method: $('#payment_method').val(),
                note: $('#note').val(),
                items: items,
            };

            $.ajax({
                url: "{{ route('admin.pharmacy_sales.store') }}",
                method: 'POST',
                data: payload,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function (response) {
                    alert('Sale completed successfully');

                    const saleId = response && response.sale_id ? response.sale_id : null;
                    if (saleId) {
                        const invoiceUrl = `{{ route('admin.pharmacy_sales.pdf-preview', ':id') }}`.replace(':id', saleId);
                        setPrintButtons(invoiceUrl);
                    }

                    // Reset form for creating another sale (stay on this page)
                    selectedItems = [];
                    renderTable();

                    $('#customer_id').val('');
                    $('#customer_name').val('');
                    $('#dr_refer_id').val('');
                    $('#dr_refer_name').val('');

                    $('#sale_date').val("{{ date('Y-m-d') }}");
                    $('#discount_amount').val(0);
                    $('#paid_amount').val(0);
                    $('#payment_method').val('');
                    $('#note').val('');

                    recalcTotals();
                },
                error: function (xhr) {
                    let msg = 'Failed to complete sale';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        if (xhr.responseJSON.errors && xhr.responseJSON.errors.length) {
                            msg += '\n' + xhr.responseJSON.errors.join('\n');
                        }
                    }
                    alert(msg);
                }
            });
        });

        // Initial totals
        recalcTotals();
    });
</script>
@endpush

