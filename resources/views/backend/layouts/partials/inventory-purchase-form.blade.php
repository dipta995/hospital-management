@php
    $isEdit = !empty($purchase);
    $purchaseItems = $purchaseItems ?? collect();
    $paidAmount = $isEdit ? ($purchase->purchase_paid_sum_amount ?? optional($purchase->purchasePaid->first())->amount ?? 0) : 0;
@endphp

<div id="purchase-form-errors" class="alert alert-danger d-none mb-3"></div>

<form method="post" action="javascript:void(0);" id="purchase-form">
    @csrf

    <div class="pharm-pos-grid pharm-purchase-grid">
        <div class="pharm-purchase-main">
            <div class="pharm-pos-panel mb-3">
                <div class="pharm-pos-panel-head"><i class="fas fa-truck"></i> Supplier & Date</div>
                <div class="pharm-pos-panel-body">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label">Supplier <span class="text-danger">*</span></label>
                            <select id="supplier_id" class="form-select supplier-select" required>
                                <option value="">Select supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" @selected($isEdit && $purchase->supplier_id == $supplier->id)>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Purchase Date <span class="text-danger">*</span></label>
                            <input type="date" id="purchase_date" class="form-control"
                                value="{{ $isEdit ? $purchase->purchase_date : date('Y-m-d') }}" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pharm-pos-panel mb-3">
                <div class="pharm-pos-panel-head"><i class="fas fa-plus-circle"></i> Add Item to Purchase</div>
                <div class="pharm-pos-panel-body">
                    <div class="row g-2 align-items-end">
                        <div class="col-lg-5 col-md-6">
                            <label class="form-label">Inventory Item</label>
                            <select id="draft_item" class="form-select">
                                <option value="">Search & select item...</option>
                                @foreach($items as $invItem)
                                    <option value="{{ $invItem->id }}"
                                        data-name="{{ e($invItem->name) }}"
                                        data-code="{{ e($invItem->code ?? '') }}">
                                        {{ $invItem->name }}@if($invItem->code) ({{ $invItem->code }})@endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4 col-md-2 col-lg-1">
                            <label class="form-label">Qty</label>
                            <input type="number" min="1" step="1" id="draft_qty" class="form-control" value="1">
                        </div>
                        <div class="col-4 col-md-2 col-lg-2">
                            <label class="form-label">Unit Price</label>
                            <input type="number" step="0.01" min="0" id="draft_price" class="form-control" placeholder="0.00">
                        </div>
                        <div class="col-4 col-md-2 col-lg-1">
                            <label class="form-label">Disc.</label>
                            <input type="number" step="0.01" min="0" id="draft_discount" class="form-control" value="0">
                        </div>
                        <div class="col-md-4 col-lg-2">
                            <label class="form-label">Expiry</label>
                            <input type="date" id="draft_expiry" class="form-control">
                        </div>
                        <div class="col-md-4 col-lg-1 d-grid">
                            <button type="button" class="btn btn-success" id="add-line-btn">
                                <i class="fas fa-plus"></i> Add
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pharm-pos-panel">
                <div class="pharm-pos-panel-head">
                    <span><i class="fas fa-list"></i> Purchase Lines</span>
                    <span class="badge bg-primary ms-auto" id="line-count-badge">0 items</span>
                </div>
                <div class="table-responsive">
                    <table class="table pharm-cart-table pharm-purchase-table mb-0">
                        <thead>
                        <tr>
                            <th>Item</th>
                            <th class="pharm-col-qty">Qty</th>
                            <th class="pharm-col-price">Price</th>
                            <th class="pharm-col-disc">Disc.</th>
                            <th class="text-end pharm-col-total">Total</th>
                            <th class="pharm-col-expiry">Expiry</th>
                            <th class="pharm-col-action"></th>
                        </tr>
                        </thead>
                        <tbody id="step1-items">
                        @foreach($purchaseItems as $line)
                            @include('backend.layouts.partials.inventory-purchase-item-row', [
                                'line' => $line,
                                'item' => $line->item ?? null,
                            ])
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div id="purchase-empty-state" class="pharm-purchase-empty @if($purchaseItems->isNotEmpty()) d-none @endif">
                    <i class="fas fa-box-open"></i>
                    <p>No items added yet</p>
                    <span class="small text-muted">Select an item above and click <strong>Add</strong></span>
                </div>
            </div>
        </div>

        <div class="pharm-pos-panel pharm-purchase-summary sticky-top">
            <div class="pharm-pos-panel-head"><i class="fas fa-receipt"></i> Payment Summary</div>
            <div class="pharm-pos-panel-body">
                <div class="pharm-summary-row">
                    <span>Line items</span>
                    <strong id="line-count">0</strong>
                </div>
                <div class="pharm-summary-row">
                    <span>Subtotal</span>
                    <strong id="subtotal_display">৳ 0.00</strong>
                </div>
                <div class="pharm-summary-row total mb-3">
                    <span>Total Cost</span>
                    <span id="total_display">৳ 0.00</span>
                </div>

                <input type="hidden" id="total_cost" value="{{ $isEdit ? $purchase->total_cost : 0 }}">

                <div class="mb-3">
                    <label class="form-label">Paid Amount</label>
                    <input type="number" step="0.01" min="0" id="paid_amount" class="form-control form-control-lg"
                        value="{{ $paidAmount }}" @if($isEdit) readonly @endif>
                </div>
                <div class="mb-3">
                    <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                    <select class="form-select" id="payment_method" @if($isEdit) disabled @endif required>
                        <option value="">Select method</option>
                        @foreach(\App\Models\Payment::$paymentStatusArray as $method)
                            <option value="{{ $method }}" @selected(!$isEdit && $loop->first)>{{ $method }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label">Due Amount</label>
                    <div class="pharm-due-display" id="due_display">৳ 0.00</div>
                    <input type="hidden" id="due_amount" value="{{ $isEdit ? $purchase->due_amount : 0 }}">
                </div>

                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-crud-submit btn-lg" id="submit-form">
                        <i class="fas fa-save"></i> {{ $isEdit ? 'Update Purchase' : 'Save Purchase' }}
                    </button>
                    <a href="{{ route('admin.purchases.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</form>

<script type="text/template" id="purchase-item-row-template">
<tr class="item-row">
    <td>
        <div class="pharm-cart-product">
            <strong class="product-name"></strong>
            <div class="small text-muted item-code d-none"></div>
        </div>
        <input type="hidden" class="item_id" value="">
    </td>
    <td class="pharm-col-qty"><input type="number" min="1" step="1" class="form-control form-control-sm quantity" value="1"></td>
    <td class="pharm-col-price"><input type="number" step="0.01" min="0" class="form-control form-control-sm unit_price" value="0"></td>
    <td class="pharm-col-disc"><input type="number" step="0.01" min="0" class="form-control form-control-sm discount_amount" value="0"></td>
    <td class="text-end pharm-col-total"><span class="line-total fw-semibold text-primary">৳ 0.00</span></td>
    <td class="pharm-col-expiry"><input type="date" class="form-control form-control-sm expiry_date" value=""></td>
    <td class="text-end pharm-col-action"><button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="fas fa-times"></i></button></td>
</tr>
</script>
