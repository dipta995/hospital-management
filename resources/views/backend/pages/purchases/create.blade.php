@extends('backend.layouts.master')
@section('title')
    Create New {{ $pageHeader['title'] }}
@endsection
@push('styles')

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
                            @include('backend.layouts.partials.message')
                            <form method="post" action="{{ route($pageHeader['store_route']) }}">
                                @csrf
                                <fieldset>
                                    <h4 class="bg-info">Step 1</h4>
                                    <div id="step1-items">
                                        <div class="row item-row">
                                            <div class="form-group col-md-4">
                                                <label>Item Name </label>
                                                <select id="item_id" class="form-control item">
                                                    <option value="">--Choose--</option>
                                                    @foreach($items as $item)
                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Quantity</label>
                                                <input type="number" class="form-control quantity">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Unit Price</label>
                                                <input type="number" class="form-control unit_price">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Discount Amount</label>
                                                <input type="number" class="form-control discount_amount">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Expiry Date</label>
                                                <input type="date" class="form-control expiry_date">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <button type="button" class="btn btn-danger remove-item mt-4">Remove</button>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-primary mt-2" id="add-item">+ Add Another</button>

                                    <h4 class="bg-info">Step 2</h4>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label>Supplier</label>
                                            <select id="supplier_id" class="form-control">
                                                @foreach($suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Purchase Date</label>
                                            <input type="date" id="purchase_date" class="form-control">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Total Cost</label>
                                            <input type="number" id="total_cost" class="form-control">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Paid Amount</label>
                                            <input type="number" id="paid_amount" class="form-control">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Due Amount</label>
                                            <input type="number" id="due_amount" class="form-control">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="paymentMethod" class="form-label">Payment
                                                Method</label>
                                            <select class="form-select" name="payment_method"
                                                    id="payment_method" required>
                                                <option value="" disabled selected>Select a method
                                                </option>
                                                @foreach(\App\Models\Payment::$paymentStatusArray as $item)
                                                    <option value="{{ $item }}">{{ $item }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-success float-end mt-2" id="submit-form">Create</button>
                                </fieldset>

                                <input type="hidden" id="items_data" name="items_data">

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
         <!-- partial -->
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            let items = [];

            // Add another item row
            $("#add-item").click(function () {
                let newRow = $(".item-row:first").clone();
                newRow.find("input").val(""); // Clear input fields
                $("#step1-items").append(newRow);
            });

            // Remove an item row
            $(document).on("click", ".remove-item", function () {
                if ($(".item-row").length > 1) {
                    $(this).closest(".item-row").remove();
                }
            });

            // Calculate due amount automatically
            $("#paid_amount, #total_cost").on("input", function () {
                let total = parseFloat($("#total_cost").val()) || 0;
                let paid = parseFloat($("#paid_amount").val()) || 0;
                let due = total - paid;
                $("#due_amount").val(due.toFixed(2));
            });

            // Submit form data
            $("#submit-form").click(function () {
                items = []; // Reset items array

                $(".item-row").each(function () {
                    let item = {
                        item_id: $(this).find(".item").val(),
                        quantity: $(this).find(".quantity").val(),
                        unit_price: $(this).find(".unit_price").val(),
                        discount_amount: $(this).find(".discount_amount").val(),
                        expiry_date: $(this).find(".expiry_date").val(),
                    };
                    if (item.item_id) items.push(item);
                });

                let data = {
                    items: items,
                    supplier_id: $("#supplier_id").val(),
                    purchase_date: $("#purchase_date").val(),
                    total_cost: $("#total_cost").val(),
                    paid_amount: $("#paid_amount").val(),
                    due_amount: $("#due_amount").val(),
                    payment_method: $("#payment_method").val(),
                };

                $.ajax({
                    url: "{{ route('admin.purchases.store') }}",
                    method: "POST",
                    data: data,
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        alert("Purchase added successfully!");
                        location.reload();
                    },
                    error: function (error) {
                        console.log(error);
                        alert("Something went wrong!");
                    }
                });
            });
        });
    </script>
@endpush

