<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Details - Admit #{{ $admit->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1100;
        }

        .content {
            margin: 80px 20px 20px;
            font-size: 14px;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }

        @media print {
            .print-button {
                display: none;
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
    <div class="mb-4 text-center">
        <h3 class="mb-0">{{ \App\Models\Setting::get('company_name') }}</h3>
        <p class="mb-0">{!! \App\Models\Setting::get('address') !!}</p>
        <p class="mb-0">Mobile: {{ \App\Models\Setting::get('phone_one') }}, {{ \App\Models\Setting::get('phone_two') }}</p>
        <p class="mb-0">Email: {{ \App\Models\Setting::get('email') }}</p>
    </div>

    <h4 class="section-title">Patient Details</h4>
    <table class="table table-bordered table-sm mb-4">
        <tr>
            <th style="width: 25%">Patient ID</th>
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

    <h4 class="section-title">Admit Details</h4>
    <table class="table table-bordered table-sm mb-4">
        <tr>
            <th style="width: 25%">Admit ID</th>
            <td>{{ $admit->id }}</td>
        </tr>
        <tr>
            <th>Admit Date</th>
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
            <th>Received By</th>
            <td>{{ $admit->received_by }}</td>
        </tr>
        <tr>
            <th>NID</th>
            <td>{{ $admit->nid }}</td>
        </tr>
        <tr>
            <th>Clinical Diagnosis</th>
            <td>{{ $admit->clinical_diagnosis }}</td>
        </tr>
        <tr>
            <th>PC Refer</th>
            <td>{{ $admit->reefer?->name }}</td>
        </tr>
        <tr>
            <th>Doctor</th>
            <td>{{ $admit->drreefer?->name }}</td>
        </tr>
        <tr>
            <th>Note</th>
            <td>{{ $admit->note }}</td>
        </tr>
    </table>
</div>

</body>
</html>
