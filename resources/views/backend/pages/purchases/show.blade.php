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
            <h2>Purchase Details</h2>
            <a href="{{ route($pageHeader['index_route']) }}" class="btn btn-secondary mb-3">Back</a>
            <div class="col-md-2">
                <a class="btn btn-info" data-bs-toggle="modal"
                   data-bs-target="#referPaymentModal"> Payment</a>
            </div>
            <!-- Bootstrap Modal -->
            <div class="modal fade" id="referPaymentModal" tabindex="-1"
                 aria-labelledby="referPaymentModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h5 class="modal-title" id="referPaymentModalLabel">Refer
                                Payment</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                        </div>
                        <!-- Modal Body -->
                        <form method="post" action="{{ route('admin.purchases.payment') }}">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="referName" class="form-label">Refer Name</label>
                                    <input type="text" value="{{ $purchase->supplier->name }}" name="supplier_name">

                                    <input type="hidden" class="form-control" name="purchase_id"
                                           id="purchase_id"
                                           value="{{ $purchase->id }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount</label>
                                    <input type="number" class="form-control" name="amount" value="{{ $purchase->total_cost-$purchase->purchase_paid_sum_amount }}"
                                           id="amount"
                                           required>
                                    @if ($errors->has('amount'))
                                        <div class="alert alert-danger">
                                            {{ $errors->first('amount') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label for="account_no" class="form-label">Account No
                                        </label>
                                    <input type="text" class="form-control" id="account_no"
                                           name="account_no"
                                           >
                                </div>
                                <div class="mb-3">
                                    <label for="date" class="form-label"> Date</label>
                                    <input type="date" class="form-control" id="date"
                                           name="date"
                                           required>
                                </div>
                                <div class="mb-3">
                                    <label for="paymentMethod" class="form-label">Payment
                                        Method</label>
                                    <select class="form-select" name="payment_type"
                                            id="payment_type" required>
                                        <option value="" disabled selected>Select a method
                                        </option>
                                        @foreach(\App\Models\Payment::$paymentStatusArray as $item)
                                            <option value="{{ $item }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <!-- Modal Footer -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    Pay
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card p-3">
                <h4>Supplier: {{ $purchase->supplier->name }}</h4>
                <p><strong>Purchase Date:</strong> {{ $purchase->purchase_date }}</p>
                <p><strong>Total Cost:</strong> Taka {{ $purchase->total_cost }}</p>
                <p><strong>Paid Amount:</strong>Taka {{ $purchase->purchase_paid_sum_amount }}</p>
                <p><strong>Due Amount:</strong> Taka {{ $purchase->total_cost-$purchase->purchase_paid_sum_amount }}</p>
            </div>

            <h3 class="mt-4">Purchased Items</h3>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th>Supplier</th>
                    <th>Quantity</th>
                    <th>Left</th>
                    <th>Unit Price</th>
                    <th>Discount</th>
                    <th>Expiry Date</th>
                </tr>
                </thead>
                <tbody>
                @foreach($purchase->purchaseItems as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->item->name }}</td>
                        <td>{{ $item->supplier->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->quantity_spend }}</td>
                        <td>Taka {{ number_format($item->unit_price, 2) }}</td>
                        <td>Taka {{ number_format($item->discount_amount, 2) }}</td>
                        <td>{{ $item->expiry_date ?? 'N/A' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- main-panel ends -->
@endsection

@push('scripts')

@endpush
