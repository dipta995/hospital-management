@php
    $line = $line ?? null;
    $item = $item ?? ($line->item ?? null);
    $qty = old('quantity', $line->quantity ?? '');
    $unitPrice = old('unit_price', $line->unit_price ?? '');
    $discount = old('discount_amount', $line->discount_amount ?? '');
    $expiry = old('expiry_date', isset($line->expiry_date) ? \Carbon\Carbon::parse($line->expiry_date)->format('Y-m-d') : '');
    $itemId = old('item_id', $line->item_id ?? '');
    $itemName = $item->name ?? 'Unknown item';
    $itemCode = $item->code ?? null;
    $lineTotal = ($qty !== '' && $unitPrice !== '') ? max(((float)$qty * (float)$unitPrice) - (float)$discount, 0) : 0;
@endphp
<tr class="item-row">
    <td>
        <div class="pharm-cart-product">
            <strong class="product-name">{{ $itemName }}</strong>
            @if($itemCode)
                <div class="small text-muted"><code>{{ $itemCode }}</code></div>
            @endif
        </div>
        <input type="hidden" class="item_id" value="{{ $itemId }}">
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
        <button type="button" class="btn btn-sm btn-outline-danger remove-item" title="Remove"><i class="fas fa-times"></i></button>
    </td>
</tr>
