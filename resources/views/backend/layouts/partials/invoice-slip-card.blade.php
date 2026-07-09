@php
    $slipVariant = $slipVariant ?? 'old';
    $slipInvoice = $slipInvoice ?? null;
    $slipProducts = $slipProducts ?? [];
    $slipPaid = (float) ($slipPaid ?? 0);
    $slipBodyId = $slipBodyId ?? null;
    $slipTitle = match ($slipVariant) {
        'delete' => app()->getLocale() === 'bn' ? 'মুছতে চাওয়া ইনভয়েস' : 'Invoice to Delete',
        'new' => app()->getLocale() === 'bn' ? 'নতুন ইনভয়েস' : 'New Invoice',
        default => app()->getLocale() === 'bn' ? 'পুরনো ইনভয়েস' : 'Old Invoice',
    };
    $slipSubtitle = match ($slipVariant) {
        'delete' => app()->getLocale() === 'bn' ? 'মুছে ফেলার আগে ভালো করে দেখুন' : 'Review before deleting',
        'new' => app()->getLocale() === 'bn' ? 'আপনি যা পরিবর্তন করছেন' : 'Your changes',
        default => app()->getLocale() === 'bn' ? 'এখন যা সেভ আছে' : 'Currently saved',
    };
    $total = (float) ($slipInvoice->total_amount ?? 0);
    $due = $total - $slipPaid;
@endphp
<div class="inv-slip-card inv-slip-{{ $slipVariant }}" data-slip-variant="{{ $slipVariant }}">
    <div class="inv-slip-card-head">
        <div>
            <div class="inv-slip-card-label">{{ $slipTitle }}</div>
            <div class="inv-slip-card-sub">{{ $slipSubtitle }}</div>
        </div>
        <span class="inv-slip-status {{ $due > 0.009 ? 'is-due' : ($due < -0.009 ? 'is-over' : 'is-paid') }}" data-slip-status>
            @if($due > 0.009)
                {{ app()->getLocale() === 'bn' ? 'বাকি' : 'DUE' }}
            @elseif($due < -0.009)
                {{ app()->getLocale() === 'bn' ? 'অতিরিক্ত' : 'OVER' }}
            @else
                {{ app()->getLocale() === 'bn' ? 'পরিশোধিত' : 'PAID' }}
            @endif
        </span>
    </div>

    <div class="inv-slip-card-body" @if($slipBodyId) id="{{ $slipBodyId }}" @endif>
        @if($slipInvoice)
            <div class="inv-slip-meta">
                <div><span>{{ app()->getLocale() === 'bn' ? 'বিল নং' : 'Bill No' }}</span><strong data-slip-field="invoice_number">{{ $slipInvoice->invoice_number }}</strong></div>
                <div><span>{{ app()->getLocale() === 'bn' ? 'রোগী' : 'Patient' }}</span><strong data-slip-field="patient_name">{{ $slipInvoice->patient_name }}</strong></div>
                <div><span>{{ app()->getLocale() === 'bn' ? 'ফোন' : 'Phone' }}</span><strong data-slip-field="patient_phone">{{ $slipInvoice->patient_phone }}</strong></div>
                <div><span>{{ app()->getLocale() === 'bn' ? 'ডাক্তার' : 'Doctor' }}</span><strong data-slip-field="doctor_name">{{ $slipInvoice->reeferDr->name ?? $slipInvoice->dr_name ?? '—' }}</strong></div>
            </div>

            <div class="inv-slip-tests-title">{{ app()->getLocale() === 'bn' ? 'টেস্ট তালিকা' : 'Test List' }}</div>
            <ul class="inv-slip-tests" data-slip-tests>
                @forelse($slipProducts as $product)
                    <li>
                        <span class="inv-slip-test-name">{{ $product['product_name'] ?? $product['name'] ?? '—' }}</span>
                        <span class="inv-slip-test-price">৳ {{ number_format((float) ($product['price'] ?? 0), 2) }}</span>
                    </li>
                @empty
                    <li class="inv-slip-empty">{{ app()->getLocale() === 'bn' ? 'কোনো টেস্ট নেই' : 'No tests' }}</li>
                @endforelse
            </ul>

            <div class="inv-slip-totals">
                <div class="inv-slip-total-row">
                    <span>{{ app()->getLocale() === 'bn' ? 'মোট বিল' : 'Total Bill' }}</span>
                    <strong data-slip-field="total_amount">৳ {{ number_format($total, 2) }}</strong>
                </div>
                <div class="inv-slip-total-row">
                    <span>{{ app()->getLocale() === 'bn' ? 'ছাড়' : 'Discount' }}</span>
                    <strong data-slip-field="discount_amount">৳ {{ number_format((float) ($slipInvoice->discount_amount ?? 0), 2) }}</strong>
                </div>
                <div class="inv-slip-total-row">
                    <span>{{ app()->getLocale() === 'bn' ? 'পরিশোধ' : 'Paid' }}</span>
                    <strong data-slip-field="paid_amount">৳ {{ number_format($slipPaid, 2) }}</strong>
                </div>
                <div class="inv-slip-total-row highlight">
                    <span>{{ app()->getLocale() === 'bn' ? 'বাকি' : 'Due' }}</span>
                    <strong data-slip-field="due_amount">৳ {{ number_format($due, 2) }}</strong>
                </div>
            </div>
        @else
            <div class="inv-slip-loading">{{ app()->getLocale() === 'bn' ? 'আপডেট হচ্ছে...' : 'Updating...' }}</div>
        @endif
    </div>
</div>
