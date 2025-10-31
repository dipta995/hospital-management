<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Printable Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 200px;
            text-align: center;
            padding: 10px;
            z-index: 1000;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1100;
        }

        .content {
            margin: 220px 20px 220px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 6px;
            font-size: 13px;
        }

        .footer-table th {
            border: none;
        }

        @media print {
            .print-button {
                display: none;
            }

            .header, .footer {
                position: fixed;
            }

            .content {
                margin: 220px 20px 200px;
            }
        }
    </style>
</head>
<body>

<!-- Print Button -->
<div class="print-button">
    <button onclick="window.print()" class="btn btn-outline-primary btn-sm">
        🖨️ Print
    </button>
</div>

<!-- Header -->
<div class="header">
    <!-- Optionally add logo or header content -->
</div>

<!-- Main Content -->
<divz class="content">
    <table class="table table-bordered">
        <tr>
            <th>ID No:</th>
            <td>{{ $invoice->invoice->invoice_number }}</td>
            <th>Date:</th>
            <td>{{ $invoice->invoice->created_at }}</td>
        </tr>
        <tr>
            <th>Patient Name:</th>
            <td>{{ $invoice->invoice->patient_name }}</td>
            <th>Age:</th>
            <td>{{ $invoice->invoice->patient_age_year ?? 0 }}</td>
        </tr>
        <tr>
            <th>Referred by:</th>
            <td>{{ $invoice->invoice->reeferDr->name ?? 'N/A' }}</td>
            <th>Sex:</th>
            <td>{{ $invoice->invoice->patient_gender ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Specimen:</th>
            <td colspan="3">{{ $invoice->reportDemo->type ?? 'N/A' }}</td>
        </tr>
    </table>

    <!-- Dynamic Report Content -->
    {!! $invoice->report !!}

    <!-- Footer Content -->
    <table class="footer-table" style="width: 100%; margin-top: 50px;">
        <tr>
            <th style="text-align: left;">
                {!! \App\Models\Setting::get('footer_test_report_left') !!}
            </th>
            <th style="text-align: center;">
                {!! \App\Models\Setting::get('footer_test_report_center') !!}
            </th>
            <th style="text-align: right;">
                {!! \App\Models\Setting::get('footer_test_report_right') !!}
            </th>
        </tr>
    </table>
</divz>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
