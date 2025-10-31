@extends('backend.layouts.master')

@section('title')
    View {{ $pageHeader['title'] }}
@endsection

@section('admin-content')
    <div class="container mt-5">

        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h3 class="mb-0">Prescription Details</h3>
            </div>
            <div class="card-body">

                <div class="text-center mb-4">
                    <h4 class="font-weight-bold">Doctor's Prescription</h4>
                    <p class="mb-1"><strong>Doctor:</strong> {{ $prescription->doctor->name ?? 'N/A' }}</p>
                    <p><strong>Issued Date:</strong> {{ $prescription->created_at->format('d M Y') }}</p>
                </div>

                <hr>


                <div class="row mb-4">
                    <div class="col-md-3">
                        <strong>Patient Name:</strong>
                        <p>{{ $prescription->invoice->patient_name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Age:</strong>
                        <p>{{ $prescription->invoice->patient_age_year ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Blood Group:</strong>
                        <p>{{ $prescription->invoice->patient_blood_group ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-3">
                        <strong>Gender:</strong>
                        <p>{{ $prescription->invoice->patient_gender ?? 'N/A' }}</p>
                    </div>
                </div>

                <hr>


                <div class="row">

                    <div class="col-md-3">
                        <h4 class="font-weight-bold">CC </h4>
                        <div class="mb-3">
                            <p>{{ $prescription->diagnosis ?? 'N/A' }}</p>
                        </div>
                        <h4 class="font-weight-bold">OE </h4>
                        <div class="mb-3">
                            <p>{{ $prescription->investigation ?? 'N/A' }}</p>
                        </div>
                        <h4 class="font-weight-bold">Advice </h4>
                        <div class="mb-3">
                            @foreach($tests as $item)
                            <p>{{ $item->product->name }},</p>
                            @endforeach
                        </div>
                    </div>


                    <div class="col-md-9">
                        <h4 class="font-weight-bold">Rx, </h4>
                        @foreach($prescription->drugs as $index => $drug)
                            <div class="mb-3">
                                <strong>{{ $index + 1 }}.</strong>
                                <strong></strong> {{ $drug->name }}
                                <strong></strong> {{ $drug->rule }}
                                <strong></strong> {{ $drug->time }}
                                <strong></strong> {{ $drug->duration }}
                                <strong></strong> {{ $drug->note }}
                            </div>

                        @endforeach
                        @if($prescription->drugs->isEmpty())
                            <p>No prescribed drugs found for this prescription.</p>
                        @endif
                    </div>

                </div>



                <!-- Print Button and Back Button Section -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route($pageHeader['index_route']) }}" class="btn btn-secondary">← Back to All
                        Prescriptions</a>
                    <button class="btn btn-success" onclick="window.print();">Print Prescription</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>

        @media print {
            body {
                margin: 0;
                padding: 0;
                font-family: Arial, sans-serif;
            }

            body * {
                visibility: hidden;
            }

            .container, .container * {
                visibility: visible;
            }

            .container {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                padding: 10px;
                box-sizing: border-box;
            }


            .row {
                display: flex;
                justify-content: space-between;
                flex-wrap: wrap;
            }

            .col-md-6 {
                width: 50%;
                margin-bottom: 10px;
            }


            .row.mb-4 {
                display: flex;
                justify-content: space-between;
            }

            .col-md-3 {
                width: 23%;
            }


            .table, .table th, .table td {
                display: none;
            }


            h4.font-weight-bold {
                margin-top: 20px;
                font-size: 22px;
            }

            .card-body div {
                font-size: 14px;
            }

            .card-body p {
                margin-bottom: 8px;
            }


            hr {
                margin: 10px 0;
            }


            .btn {
                display: none;
            }
        }


        .card-body p {
            margin: 0;
        }

        .font-weight-bold {
            font-weight: 600;
        }

        .text-center h4 {
            font-size: 24px;
            margin: 10px 0;
        }

        .row .col-md-3, .row .col-md-2 {
            font-size: 14px;
        }

        .card-header h3 {
            font-size: 28px;
            font-weight: bold;
        }

        .card-body {
            font-family: 'Arial', sans-serif;
            font-size: 16px;
        }

        .table-bordered {
            border: 1px solid #ddd;
        }

        .table th, .table td {
            padding: 12px;
            vertical-align: middle;
            text-align: center;
        }
    </style>
@endpush
