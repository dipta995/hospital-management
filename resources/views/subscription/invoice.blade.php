<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $payment->invoice_no }}</title>
    <style>
        body { font-family: DejaVu Sans, Helvetica, Arial, sans-serif; font-size: 12px; color: #1f2937; margin: 0; background: {{ $isPdf ? '#fff' : '#f3f4f6' }}; }
        .sheet { max-width: 760px; margin: {{ $isPdf ? '0' : '24px auto' }}; background: #fff; padding: 28px 32px; {{ $isPdf ? '' : 'box-shadow: 0 1px 4px rgba(0,0,0,.08); border-radius: 8px;' }} }
        table { width: 100%; border-collapse: collapse; }
        .head td { vertical-align: top; }
        .company { font-size: 18px; font-weight: bold; }
        .muted { color: #6b7280; font-size: 11px; }
        .title { font-size: 22px; font-weight: bold; text-align: right; color: #111827; letter-spacing: 1px; }
        .paid { display: inline-block; margin-top: 6px; padding: 3px 12px; border: 2px solid #16a34a; color: #16a34a; font-weight: bold; border-radius: 4px; }
        .meta td { padding: 3px 0; }
        .box { border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px 12px; }
        .items th { background: #f1f5f9; text-align: left; padding: 8px; border-bottom: 1px solid #cbd5e1; font-size: 11px; }
        .items td { padding: 8px; border-bottom: 1px solid #e5e7eb; }
        .right { text-align: right; }
        .total td { font-weight: bold; font-size: 14px; border-top: 2px solid #111827; padding-top: 8px; }
        .actions { text-align: center; margin: 16px 0 0; }
        .actions a, .actions button { display: inline-block; margin: 0 4px; padding: 8px 16px; border-radius: 6px; border: 1px solid #2563eb; background: #2563eb; color: #fff; text-decoration: none; font-size: 13px; cursor: pointer; }
        .actions .outline { background: #fff; color: #2563eb; }
        @media print { body { background: #fff; } .sheet { box-shadow: none; margin: 0; } .actions { display: none; } }
    </style>
</head>
<body>
<div class="sheet">
    <table class="head">
        <tr>
            <td style="width: 60%;">
                @if($isPdf && $logoPath)
                    <img src="{{ $logoPath }}" alt="Logo" style="max-height: 48px; margin-bottom: 6px;"><br>
                @elseif(!$isPdf && $logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo" style="max-height: 48px; margin-bottom: 6px;"><br>
                @endif
                <div class="company">{{ $company }}</div>
                @if($address)<div class="muted">{{ $address }}</div>@endif
                @if($phone)<div class="muted">Phone: {{ $phone }}</div>@endif
                @if($email)<div class="muted">Email: {{ $email }}</div>@endif
            </td>
            <td style="width: 40%; text-align: right;">
                <div class="title">INVOICE</div>
                <div class="paid">PAID</div>
                <table class="meta" style="margin-top: 8px;">
                    <tr><td class="muted right">Invoice No:</td><td class="right"><strong>{{ $payment->invoice_no }}</strong></td></tr>
                    <tr><td class="muted right">Date:</td><td class="right">{{ optional($payment->invoiced_at ?? $payment->approved_at)->timezone('Asia/Dhaka')->format('d M Y, h:i A') }}</td></tr>
                    @if($payment->invoice_number)
                        <tr><td class="muted right">Order Ref:</td><td class="right">{{ $payment->invoice_number }}</td></tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <table style="margin-top: 18px;">
        <tr>
            <td style="width: 50%; padding-right: 8px; vertical-align: top;">
                <div class="box">
                    <div class="muted">Billed To</div>
                    <strong>{{ $payment->payer_name ?: ($payment->submittedByAdmin->name ?? $company) }}</strong>
                    @if($payment->sender_number)<div>{{ $payment->sender_number }}</div>@endif
                    @if($payment->payer_email)<div>{{ $payment->payer_email }}</div>@endif
                </div>
            </td>
            <td style="width: 50%; padding-left: 8px; vertical-align: top;">
                <div class="box">
                    <div class="muted">Payment</div>
                    <div>Method: <strong>{{ $payment->payment_method ?: ($payment->gateway ? ucfirst($payment->gateway) : 'Manual') }}</strong></div>
                    <div>Transaction ID: <strong>{{ $payment->transaction_id }}</strong></div>
                    <div>Paid On: {{ optional($payment->transaction_date)->format('d M Y') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="items" style="margin-top: 18px;">
        <thead>
        <tr>
            <th style="width: 8%;">#</th>
            <th>Description</th>
            <th style="width: 30%;">Period</th>
            <th class="right" style="width: 18%;">Amount (BDT)</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>1</td>
            <td>Software subscription renewal (1 month)</td>
            <td>{{ $periodStart->format('d M Y') }} &ndash; {{ $periodEnd->format('d M Y') }}</td>
            <td class="right">{{ number_format((float) $payment->amount, 2) }}</td>
        </tr>
        </tbody>
        <tfoot>
        <tr class="total">
            <td colspan="3" class="right">Total Paid</td>
            <td class="right">{{ number_format((float) $payment->amount, 2) }}</td>
        </tr>
        </tfoot>
    </table>

    <p class="muted" style="margin-top: 24px;">This is a system generated invoice and does not require a signature.</p>

    @unless($isPdf)
        <div class="actions">
            <button type="button" onclick="window.print()">Print</button>
            <a href="{{ $invoiceUrl }}?download=pdf" class="outline">Download PDF</a>
        </div>
    @endunless
</div>
</body>
</html>
