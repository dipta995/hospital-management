<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Printable Report</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* General styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: auto; /* Set the total page height */
        }

        /* Fixed header styles */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 200px;
            background-color: rgba(0, 123, 255, 0);
            color: white;
            text-align: center;
            line-height: 1.5;
            z-index: 1000;
            padding: 10px;
        }

        /* Fixed footer styles */

        /* Content area with dynamic height */
        .content {
            margin: 210px 20px 210px; /* Space for header (200px) and footer (200px), with slight padding */
            height: auto !important;  /* Ensure content area takes 800px */
            overflow: hidden; /* Prevent content from spilling outside */
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 5px;
            font-size: 12px; /* Compact text size */
            text-align: left;
        }

        /* Print-specific styles */
        @media print {
            body {
                margin: 0;
                padding: 0;
                height: auto; /* Allow pages to flow naturally */
            }

            .header, .footer {
                position: fixed;
            }

            .content {
                page-break-inside: auto;
                margin: 210px 20px 210px; /* Space for header and footer */
                height: auto; /* Content adjusts based on print length */
            }

            table {
                /*page-break-inside: avoid; */
            }

            .page-break {
                /*page-break-before: always;*/
            }
        }
    </style>
</head>
<body>
<!-- Header Section -->
<div class="header">

</div>

<!-- Content Section -->
<div class="content">
    <table class="table table-bordered">
        <tr>
            <th>ID no:</th>
            <td>{{ $invoice->invoice->invoice_number }}</td>
            <th>Date:</th>
            <td>{{ $invoice->invoice->created_at }}</td>
        </tr>
        <tr>
            <th>Patient name:</th>
            <td>{{ $invoice->invoice->patient_name }}</td>
            <th>Age:</th>
            <td>{{ $invoice->invoice->patient_age_year ?? 0 }}</td>
        </tr>
        <tr>
            <th>Reffd by Dr/Prof:</th>
            <td>{{ $invoice->invoice->reeferDr->name ?? 'n/a' }}</td>
            <th>Sex:</th>
            <td>{{ $invoice->invoice->patient_gender ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Specimen:</th>
            <td colspan="3">{{ $invoice->product->name ?? 'N/A' }}</td>
        </tr>
    </table>
    <!-- Dynamic Content -->
    {!! $invoice->test_report !!}
    <div class="page-break"></div> <!-- Optional forced page break -->
    {!! \App\Models\Setting::get('footer_test_report') !!}
</div>



<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
