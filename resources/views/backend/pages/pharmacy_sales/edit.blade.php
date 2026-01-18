@extends('backend.layouts.master')
@section('title')
    Edit {{ $pageHeader['title'] }}
@endsection
@push('styles')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush
@section('admin-content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit {{ $pageHeader['title'] }}</h4>
                        @include('backend.layouts.partials.message')

                        <h4 class="card-title bg-info p-1 mt-3 mb-3">Customer</h4>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>Search (Name / Phone)</label>
                                <input type="text" id="customer_search" class="form-control" placeholder="Type name or phone">
                                <ul class="list-group" id="customer_suggestion" style="position:absolute; z-index:1000; width:100%; display:none;"></ul>
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-primary form-control" data-bs-toggle="modal" data-bs-target="#customerModal">Add New Customer</button>
                            </div>
                            <div class="col-md-5">
                                <label>Selected Customer</label>
                                <input type="text" id="customer_name" class="form-control" value="{{ optional($customer)->name }}" readonly>
                                <input type="hidden" id="customer_id" value="{{ $sale->customer_id }}">
                            </div>
                        </div>

                        <h4 class="card-title bg-info p-1 mt-3 mb-3">Doctor</h4>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="dr_refer_name">Dr Name
                                    <button type="button" class="badge bg-info openDoctorModal" data-target-input="dr_refer_name">Add Doctor</button>
                                </label>
                                <input type="text" id="dr_refer_name" class="form-control" value="{{ optional($sale->doctor)->name }}">
                                <input type="hidden" id="dr_refer_id" value="{{ $sale->dr_refer_id }}">
                            </div>
                        </div>

                        <h4 class="card-title bg-info p-1 mt-3 mb-3">Products</h4>
                        <div class="row mb-3">
                            <div class="col-md-5">
                                <label for="product_name">Product</label>
                                <input type="text" id="product_name" class="form-control" placeholder="Type product name / generic / barcode">
                                <input type="hidden" id="pharmacy_product_id">
                            </div>
                            <div class="col-md-2">
                                <label for="product_qty">Qty</label>
                                <input type="number" id="product_qty" class="form-control" value="1" min="1">
                            </div>
                            <div class="col-md-2">
                                <label for="product_price">Unit Price</label>
                                <input type="number" id="product_price" class="form-control" step="0.01">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-success w-100" id="add-product">Add</button>
                            </div>
                        </div>

                        <table class="table table-bordered" id="sale-products">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Discount</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <!-- rows via JS -->
                            </tbody>
                        </table>

                        <h4 class="card-title bg-info p-1 mt-3 mb-3">Payment</h4>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label>Sale Date</label>
                                <input type="date" id="sale_date" class="form-control" value="{{ $sale->sale_date }}">
                            </div>
                            <div class="col-md-3">
                                <label>Subtotal</label>
                                <input type="number" id="subtotal" class="form-control" readonly>
                            </div>
                            <div class="col-md-3">
                                <label>Invoice Discount</label>
                                <input type="number" id="discount_amount" class="form-control" value="{{ $sale->discount_amount }}" step="0.01">
                            </div>
                            <div class="col-md-3">
                                <label>Total</label>
                                <input type="number" id="total_amount" class="form-control" value="{{ $sale->total_amount }}" readonly>
                            </div>
                            <div class="col-md-3 mt-2">
                                <label>Paid</label>
                                <input type="number" id="paid_amount" class="form-control" value="{{ $sale->paid_amount }}" step="0.01">
                            </div>
                            <div class="col-md-3 mt-2">
                                <label>Due</label>
                                <input type="number" id="due_amount" class="form-control" value="{{ $sale->due_amount }}" readonly>
                            </div>
                            <div class="col-md-3 mt-2">
                                <label>Payment Method</label>
                                <input type="text" id="payment_method" class="form-control" value="{{ $sale->payment_method }}">
                            </div>
                            <div class="col-md-3 mt-2">
                                <label>Note</label>
                                <input type="text" id="note" class="form-control" value="{{ $sale->note }}">
                            </div>
                        </div>

                        <button type="button" class="btn btn-success float-end" id="update-sale">Update Sale</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Customer Modal (reuse) -->
<div class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">
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
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script>
    $(document).ready(function () {
        let selectedItems = [];

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
                                return {
                                    label: item.name + (item.generic_name ? ' (' + item.generic_name + ')' : ''),
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
            let invDisc = parseFloat($('#discount_amount').val()) || 0;
            let grand = subtotal - invDisc;
            $('#total_amount').val(grand.toFixed(2));
            let paid = parseFloat($('#paid_amount').val()) || 0;
            let due = grand - paid;
            $('#due_amount').val(due.toFixed(2));
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
        });

        configureAutocomplete('dr_refer_name', "{{ url('admin/get-doctors') }}", function (item) {
            $('#dr_refer_id').val(item.referID);
            $('#dr_refer_name').val(item.name);
        });

        // preload existing items
        selectedItems = [
            @foreach($sale->items as $item)
            {
                pharmacy_product_id: '{{ $item->pharmacy_product_id }}',
                name: '{{ optional($item->product)->name }}',
                quantity: {{ $item->quantity }},
                unit_price: {{ $item->unit_price }},
                discount_amount: {{ $item->discount_amount }},
                total_amount: {{ $item->total_amount }}
            }@if(!$loop->last),@endif
            @endforeach
        ];
        renderTable();

        $('#add-product').click(function () {
            let pid = $('#pharmacy_product_id').val();
            let name = $('#product_name').val().trim();
            let qty = parseFloat($('#product_qty').val()) || 0;
            let price = parseFloat($('#product_price').val()) || 0;

            if (!pid || !name || qty <= 0 || price <= 0) {
                alert('Select product with quantity and price.');
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
                discount_amount: 0,
                total_amount: total
            });

            $('#pharmacy_product_id').val('');
            $('#product_name').val('');
            $('#product_qty').val(1);
            $('#product_price').val('');

            renderTable();
        });

        $(document).on('input', '.row-qty, .row-price, .row-discount', function () {
            let tr = $(this).closest('tr');
            let idx = tr.data('index');
            selectedItems[idx].quantity = parseFloat(tr.find('.row-qty').val()) || 0;
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
        // Customer search
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

        // Save customer
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

        // Update sale
        $('#update-sale').click(function () {
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
                url: "{{ url('admin/pharmacy-sales/'.$sale->id) }}",
                method: 'POST',
                data: Object.assign(payload, {_method: 'PUT'}),
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function () {
                    alert('Sale updated successfully');
                    window.location.href = "{{ route('admin.pharmacy_sales.index') }}";
                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                    alert('Failed to update sale');
                }
            });
        });

        recalcTotals();
    });
</script>
@endpush
