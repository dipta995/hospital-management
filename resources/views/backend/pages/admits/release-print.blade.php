<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admit Summary - #{{ $admit->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
        }
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1100;
        }
        .content {
            margin: 80px auto 30px;
            padding: 20px 25px;
            max-width: 900px;
            background-color: #ffffff;
            box-shadow: 0 0 8px rgba(0,0,0,0.08);
            font-size: 13px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        .summary-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 10px;
        }
        .summary-badge {
            flex: 1 1 150px;
            padding: 8px 10px;
            border-radius: 4px;
            background-color: #f8f9fa;
            border: 1px solid #e1e5eb;
            font-size: 12px;
        }
        .summary-badge span.label {
            display: block;
            color: #6c757d;
            font-weight: 600;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .summary-badge span.value {
            font-size: 15px;
            font-weight: 600;
            color: #212529;
        }
        .table-sm th,
        .table-sm td {
            padding: 4px 8px;
        }
        .table thead th {
            background-color: #f1f3f5;
            border-bottom-width: 1px;
        }
        .meta-row {
            font-size: 12px;
            color: #6c757d;
        }
        @media print {
            .print-button {
                display: none;
            }
            body {
                background-color: #ffffff;
            }
            .content {
                margin: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

<div class="print-button">
    <button onclick="window.print()" class="btn btn-outline-primary btn-sm">
        🖨️ Print
    </button>
</div>

<div class="content">
    <div class="mb-3" style="display: table; width: 100%; border-bottom: 2px solid #000; padding-bottom: 8px;">
        <div style="display: table-cell; width: 20%; vertical-align: middle;">
            @if(\App\Models\Setting::get('logo'))
                <img src="{{ asset('images/'.\App\Models\Setting::get('logo')) }}" alt="Logo" style="height: 60px;">
            @endif
        </div>
        <div style="display: table-cell; width: 60%; text-align: left; vertical-align: middle; color:#000;">
            <h1 style="margin: 0; font-size: 20px; text-transform: uppercase;">{{ \App\Models\Setting::get('company_name') }}</h1>
            <p style="margin: 0; font-size: 11px;">{!! \App\Models\Setting::get('address') !!}</p>
            <p style="margin: 0; font-size: 11px;">Mobile: {{ \App\Models\Setting::get('phone_one') }}, {{ \App\Models\Setting::get('phone_two') }}</p>
            <p style="margin: 0; font-size: 11px;">Email: {{ \App\Models\Setting::get('email') }}</p>
        </div>
        <div style="display: table-cell; width: 20%; text-align: right; vertical-align: middle;" class="meta-row">
            <div>Admit No: <strong>{{ $admit->id }}</strong></div>
            <div>Print: <strong>{{ now('Asia/Dhaka')->format('Y-m-d H:i') }}</strong></div>
        </div>
    </div>



    <div class="row mb-3">
        <div class="col-md-6">
            <h4 class="section-title">Patient Details</h4>
            <table class="table table-bordered table-sm mb-3">
                <tr>
                    <th style="width: 35%">Patient ID</th>
                    <td>{{ $admit->user?->id }}</td>
                </tr>
                <tr>
                    <th>Name</th>
                    <td>{{ $admit->user?->name }}</td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>{{ $admit->user?->phone }}</td>
                </tr>
                <tr>
                    <th>Age</th>
                    <td>{{ $admit->user?->age }}</td>
                </tr>
                <tr>
                    <th>Gender</th>
                    <td>{{ ucfirst($admit->user?->gender) }}</td>
                </tr>
                <tr>
                    <th>Blood Group</th>
                    <td>{{ $admit->user?->blood_group }}</td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td>{{ $admit->user?->address }}</td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <h4 class="section-title">Admit Details</h4>
            <table class="table table-bordered table-sm mb-3">
                <tr>
                    <th style="width: 35%">Admit Date</th>
                    <td>{{ $admit->admit_at }}</td>
                </tr>
                <tr>
                    <th>Release Date</th>
                    <td>{{ $admit->release_at ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Bed/Cabin</th>
                    <td>{{ $admit->bed_or_cabin ?? $admit->bedCabin?->name }}</td>
                </tr>
                <tr>
                    <th>Father/Spouse</th>
                    <td>{{ $admit->father_or_spouse }}</td>
                </tr>
                <tr>
                    <th>Doctor</th>
                    <td>{{ $admit->drreefer?->name }}</td>
                </tr>
                <tr>
                    <th>PC Refer</th>
                    <td>{{ $admit->reefer?->name }}</td>
                </tr>
                <tr>
                    <th>Received By</th>
                    <td>{{ $admit->received_by }}</td>
                </tr>
                <tr>
                    <th>NID</th>
                    <td>{{ $admit->nid }}</td>
                </tr>
                <tr>
                    <th>Diagnosis</th>
                    <td>{{ $admit->clinical_diagnosis }}</td>
                </tr>
                <tr>
                    <th>Note</th>
                    <td>{{ $admit->note }}</td>
                </tr>
            </table>
        </div>
    </div>

    <h4 class="section-title">Financial Summary (This Admit)</h4>
    <div class="summary-badges mb-3">
        <div class="summary-badge">
            <span class="label">Total Amount</span>
            <span class="value">{{ number_format($total_amount, 2) }}</span>
        </div>
        <div class="summary-badge">
            <span class="label">Total Discount</span>
            <span class="value">{{ number_format($total_discount, 2) }}</span>
        </div>
        <div class="summary-badge">
            <span class="label">Net Amount</span>
            <span class="value">{{ number_format($net_total, 2) }}</span>
        </div>
        <div class="summary-badge">
            <span class="label">Total Paid</span>
            <span class="value">{{ number_format($total_paid, 2) }}</span>
        </div>
        <div class="summary-badge">
            <span class="label">Total Due</span>
            <span class="value">{{ number_format($total_due, 2) }}</span>
        </div>
        @php
            $extra_amount = $extra_amount ?? max($total_paid - $net_total, 0);
        @endphp
        @if($extra_amount > 0)
            <div class="summary-badge">
                <span class="label">Extra / Advance</span>
                <span class="value">{{ number_format($extra_amount, 2) }}</span>
            </div>
        @endif
    </div>

    <h4 class="section-title">Receipts</h4>
    <table class="table table-bordered table-sm mb-4">
        <thead>
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Receipt No</th>
            <th>Total</th>
            <th>Discount</th>
            <th>Net</th>
        </tr>
        </thead>
        <tbody>
        @forelse($receipts as $recept)
            @php
                $net = $recept->total_amount - $recept->discount_amount;
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $recept->created_at?->format('Y-m-d') }}</td>
                <td>{{ $recept->id }}</td>
                <td>{{ number_format($recept->total_amount, 2) }}</td>
                <td>{{ number_format($recept->discount_amount, 2) }}</td>
                <td>{{ number_format($net, 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">No receipts found for this admit.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h4 class="section-title">Payment Ledger</h4>
    <table class="table table-bordered table-sm mb-4">
        <thead>
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Description</th>
            <th class="text-end">Debit (Paid)</th>
            <th class="text-end">Credit (Return)</th>
            <th class="text-end">Running Paid</th>
        </tr>
        </thead>
        <tbody>
        @php
            $runningPaid = 0;
            $totalDebit = 0;
            $totalCredit = 0;
        @endphp
        @forelse($payments as $payment)
            @php
                $amount = (float) $payment->paid_amount;
                $isDebit = $amount >= 0;
                $debit = $isDebit ? $amount : 0;
                $credit = $isDebit ? 0 : abs($amount);
                $runningPaid += $amount;
                $totalDebit += $debit;
                $totalCredit += $credit;
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $payment->creation_date ?? $payment->created_at?->format('Y-m-d') }}</td>
                <td>{{ $isDebit ? 'Payment' : 'Return / Adjustment' }}</td>
                <td class="text-end">{{ $debit ? number_format($debit, 2) : '' }}</td>
                <td class="text-end">{{ $credit ? number_format($credit, 2) : '' }}</td>
                <td class="text-end">{{ number_format($runningPaid, 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">No payments recorded for this admit.</td>
            </tr>
        @endforelse
        </tbody>
        @php
            $netPaid = $totalDebit - $totalCredit;
            $finalDue = max($net_total - $netPaid, 0);
            $finalExtra = max($netPaid - $net_total, 0);
        @endphp
        <tfoot>
        <tr>
            <th colspan="3" class="text-end">Totals</th>
            <th class="text-end">{{ number_format($totalDebit, 2) }}</th>
            <th class="text-end">{{ number_format($totalCredit, 2) }}</th>
            <th class="text-end">{{ number_format($netPaid, 2) }}</th>
        </tr>
        <tr>
            <th colspan="3" class="text-end">Final Position</th>
            <th colspan="3" class="text-end">
                Net: {{ number_format($net_total, 2) }} | Paid: {{ number_format($netPaid, 2) }} |
                Due: {{ number_format($finalDue, 2) }} | Extra: {{ number_format($finalExtra, 2) }}
            </th>
        </tr>
        </tfoot>
    </table>

    <div class="row mt-5">
        <div class="col-6 text-center">
            <div style="border-top:1px solid #000; display:inline-block; padding-top:4px; min-width:180px; font-size:12px;">
                Authorized Signature
            </div>
        </div>
        <div class="col-6 text-center">
            <div style="border-top:1px solid #000; display:inline-block; padding-top:4px; min-width:180px; font-size:12px;">
                Patient / Family Member
            </div>
        </div>
    </div>
</div>

</body>
</html>
