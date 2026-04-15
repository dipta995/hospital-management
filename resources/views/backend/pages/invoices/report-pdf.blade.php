<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Report — {{ $invoice->invoice->invoice_number ?? '' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            color: #111;
            background: #fff;
        }

        /* ── Fixed header (screen + print) ── */
        .report-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: #fff;
            border-bottom: 2px solid #333;
            padding: 8px 20px;
        }

        .report-header-inner {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .report-header .logo-col {
            flex: 0 0 auto;
        }

        .report-header .logo-col img {
            height: 75px;
            width: auto;
            display: block;
        }

        .report-header .info-col {
            flex: 1 1 auto;
            text-align: center;
        }

        .report-header .info-col h1 {
            margin: 0 0 2px;
            font-size: 18px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .report-header .info-col p {
            margin: 0;
            font-size: 11px;
            line-height: 1.4;
            color: #444;
        }

        .report-header .qr-col {
            flex: 0 0 auto;
            text-align: right;
        }

        .report-header .qr-col img {
            width: 70px;
            height: 70px;
            display: block;
        }

        /* ── Print button (hidden on print) ── */
        .print-bar {
            position: fixed;
            top: 6px;
            right: 20px;
            z-index: 1200;
        }

        /* ── Main content ── */
        .report-body {
            margin-top: 115px;   /* clears fixed header */
            margin-bottom: 80px; /* clears fixed footer */
            padding: 18px 24px 0;
        }

        /* ── Patient info table ── */
        .patient-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        .patient-table th,
        .patient-table td {
            border: 1px solid #bbb;
            padding: 5px 8px;
            font-size: 12px;
        }

        .patient-table th {
            background: #f3f4f6;
            font-weight: 600;
            width: 16%;
            white-space: nowrap;
        }

        .patient-table td {
            width: 17%;
        }

        /* ── Dynamic report content (parameters table) ── */
        .report-body table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-bottom: 14px;
        }

        .report-body table th,
        .report-body table td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        .report-body table thead th {
            background: #f3f4f6;
            font-weight: 700;
            font-size: 12px;
        }

        .report-body p {
            margin: 6px 0;
            line-height: 1.5;
        }

        /* ── Divider between report & footer ── */
        .section-divider {
            border: none;
            border-top: 1px solid #ccc;
            margin: 24px 0 12px;
        }

        /* ── Fixed footer ── */
        .report-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            border-top: 1px solid #ccc;
            padding: 8px 24px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 11px;
            color: #444;
            z-index: 1000;
        }

        .report-footer .footer-col {
            flex: 1;
        }

        .report-footer .footer-col:nth-child(2) { text-align: center; }
        .report-footer .footer-col:nth-child(3) { text-align: right; }

        /* ── Print overrides ── */
        @media print {
            .print-bar {
                display: none !important;
            }

            .report-header {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
            }

            .report-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
            }

            .report-body {
                margin-top: 115px;
                margin-bottom: 80px;
            }
        }
    </style>
</head>
<body>

{{-- ── Print button ─────────────────────────────────── --}}
<div class="print-bar no-print">
    <button onclick="window.print()" class="btn btn-primary btn-sm">
        🖨️ Print
    </button>
</div>

{{-- ── Fixed Header ────────────────────────────────── --}}
<div class="report-header">
    <div class="report-header-inner">
        <div class="logo-col">
            <img src="{{ asset('images/'.\App\Models\Setting::get('logo')) }}" alt="Logo">
        </div>
        <div class="info-col">
            <h1>{{ \App\Models\Setting::get('company_name') }}</h1>
            <p>{!! \App\Models\Setting::get('address') !!}</p>
            <p>
                Mobile: {{ \App\Models\Setting::get('phone_one') }}
                @if(\App\Models\Setting::get('phone_two'))
                    , {{ \App\Models\Setting::get('phone_two') }}
                @endif
            </p>
            <p>Email: {{ \App\Models\Setting::get('email') }}</p>
        </div>
        <div class="qr-col">
            <img src="data:image/png;base64,{{ $qrcode }}" alt="QR Code">
        </div>
    </div>
</div>

{{-- ── Body ─────────────────────────────────────────── --}}
<div class="report-body">

    {{-- Patient Info --}}
    <table class="patient-table">
        <tr>
            <th>ID No</th>
            <td>{{ $invoice->invoice->invoice_number }}</td>
            <th>Date</th>
            <td>{{ \Carbon\Carbon::parse($invoice->invoice->created_at)->format('d M Y') }}</td>
        </tr>
        <tr>
            <th>Patient Name</th>
            <td>{{ $invoice->invoice->patient_name }}</td>
            <th>Age</th>
            <td>{{ $invoice->invoice->patient_age_year ?? '—' }}</td>
        </tr>
        <tr>
            <th>Referred By</th>
            <td>{{ $invoice->invoice->reeferDr->name ?? 'N/A' }}</td>
            <th>Sex</th>
            <td>{{ $invoice->invoice->patient_gender ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Specimen</th>
            <td colspan="3">{{ optional($invoice->invoiceItem->product ?? null)->name ?? 'N/A' }}</td>
        </tr>
    </table>

    {{-- Dynamic Report Content --}}
    {!! $invoice->report !!}

    <hr class="section-divider">
</div>

{{-- ── Fixed Footer ────────────────────────────────── --}}
<div class="report-footer">
    <div class="footer-col">
        {!! \App\Models\Setting::get('footer_test_report_left') !!}
    </div>
    <div class="footer-col">
        {!! \App\Models\Setting::get('footer_test_report_center') !!}
    </div>
    <div class="footer-col">
        {!! \App\Models\Setting::get('footer_test_report_right') !!}
    </div>
</div>

</body>
</html>
