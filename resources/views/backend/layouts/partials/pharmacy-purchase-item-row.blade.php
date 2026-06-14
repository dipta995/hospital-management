@php
    $line = $line ?? null;
    $product = $product ?? ($line->product ?? null);
    $qty = old('quantity', $line->quantity ?? '');
    $unitPrice = old('unit_price', $line->unit_price ?? '');
    $discount = old('discount_amount', $line->discount_amount ?? '');
    $expiry = old('expiry_date', isset($line->expiry_date) ? \Carbon\Carbon::parse($line->expiry_date)->format('Y-m-d') : '');
    $productId = old('pharmacy_product_id', $line->pharmacy_product_id ?? '');
    $productName = $product->name ?? 'Unknown product';
    $stock = $product->current_stock ?? ($line ? 0 : 0);
    $lineTotal = ($qty !== '' && $unitPrice !== '') ? max(((float)$qty * (float)$unitPrice) - (float)$discount, 0) : 0;
@endphp
<tr class="item-row">
    <td>
        <div class="pharm-cart-product">
            <strong class="product-name">{{ $productName }}</strong>
            @if($product && ($product->generic_name ?? null))
                <div class="small text-muted">{{ $product->generic_name }}</div>
            @endif
        </div>
        <input type="hidden" class="pharmacy_product_id" value="{{ $productId }}">
    </td>
    <td class="pharm-col-qty">
        <input type="number" min="1" step="1" class="form-control form-control-sm quantity" value="{{ $qty }}">
    </td>
    <td class="pharm-col-price">
        <input type="number" step="0.01" min="0" class="form-control form-control-sm unit_price" value="{{ $unitPrice }}">
    </td>
    <td class="pharm-col-disc">
        <input type="number" step="0.01" min="0" class="form-control form-control-sm discount_amount" value="{{ $discount ?: 0 }}">
    </td>
    <td class="text-end pharm-col-total">
        <span class="line-total fw-semibold text-primary">৳ {{ number_format($lineTotal, 2) }}</span>
    </td>
    <td class="pharm-col-expiry">
        <input type="date" class="form-control form-control-sm expiry_date" value="{{ $expiry }}">
    </td>
    <td class="text-end pharm-col-action">
        <button type="button" class="btn btn-sm btn-outline-danger remove-item" title="Remove line"><i class="fas fa-times"></i></button>
    </td>
</tr>
