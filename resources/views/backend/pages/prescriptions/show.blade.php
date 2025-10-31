@extends('backend.layouts.master')

@section('admin-content')
    <!-- Copy Confirmation Modal -->
   

    <div class="container-fluid mt-1">
        <div class="card shadow-lg">
            <div class="card-body p-0">
                <div class="d-flex justify-content-end p-3">

                </div>
                <!-- Prescription Template -->
                <div id="copyContent" class="prescription-container">
                    <div class="prescription-header">

                        <div class="header-content">

                            <div class="header-right">
                                <div class="doctor-name">{{ $prescription->doctor->name ?? 'N/A' }}</div>
                                <div class="qualifications">
                                {!! $prescription->doctor->designation ?? '' !!}
                                </div>
                                <p><strong>Issued Date:</strong> {{ $prescription->created_at->format('d M Y') }}</p>
                            </div>
                        </div>

                        <div class="patient-info">
                            <div class="info-field">
                                <span>Name:</span>
                                <span class="dotted-line copy-text"><p>{{ $prescription->invoice->patient_name ?? 'N/A' }}</p></span>
                            </div>
                            <div class="info-field">
                                <span>Age:</span>
                                <span class="dotted-line copy-text">{{ $prescription->invoice->patient_age_year ?? 'N/A' }}</span>
                            </div>
                            <div class="info-field">
                                <span>Date:</span>
                                <span class="dotted-line copy-text">{{ $prescription->created_at ? $prescription->created_at->format('d/m/Y') : 'N/A' }}</span>
                            </div>
                            {{-- <div class="info-field">
                                <span>Next Visit:</span>
                                <span class="dotted-line copy-text">{{ $prescription->next_appointment_date ? \Carbon\Carbon::parse($prescription->next_appointment_date)->format('d/m/Y') : 'N/A' }}</span>
                            </div> --}}
                            <div class="info-field">
                                <span>Sex:</span>
                                <span class="dotted-line copy-text">{{ $prescription->invoice->patient_gender ?? 'N/A' }}</span>
                            </div>
                            <div class="info-field">
                                <span>Blood:</span>
                                <span class="dotted-line copy-text">{{ $prescription->invoice->patient_blood_group ?? 'N/A' }}</span>
                            </div>

                        </div>

                    </div>

                    <div class="main-content">
                        <div class="sidebar">
                            <div class="sidebar-section">
                                <h5 class="section-title">(C/C)</h5>
                                <div class="diagnosis-text">
                                    @if($prescription->diagnosis)
                                        @foreach(explode("\n", $prescription->diagnosis) as $line)
                                            <div>{{ $line }}</div>
                                        @endforeach

                                    @else
                                        <div class="text-muted">No complaints recorded</div>
                                    @endif
                                </div>
                            </div>

                            <div class="sidebar-section">
                                <h5 class="section-title">(O/E)</h5>
                                <div class="investigation-text">
                                    @if($prescription->investigation)
                                        @foreach(explode("\n", $prescription->investigation) as $line)
                                            <div>{{ $line }}</div>
                                        @endforeach

                                    @else
                                        <div class="text-muted">No examination notes</div>
                                    @endif
                                </div>
                            </div>

                            <div class="sidebar-section">
                                <h5 class="section-title">Medical Advice</h5>
                                <div class="advice-text">
                                    @forelse($tests as $item)
                                        <div class="advice-item copy-text">{{ $item->product->name }}</div>
                                    @empty
                                        <div class="text-muted">No specific advice given</div>
                                    @endforelse

                                </div>
                            </div>
                        </div>

                        <div class="prescription-area">
                            <div class="rx-symbol">℞</div>
                            <div class="prescription-lines">
                                @forelse($prescription->drugs as $index => $drug)
                                    <div class="prescription-line">
                                        <div class="medicine-details">
                                            <div class="medicine-instructions">
                                                <strong class="medicine-number">{{ $index + 1 }}.</strong>
                                                <span class="medicine-name">Medicine Name: <span class="copy-text">{{ $drug->name }}</span></span>
                                                <span class="rule">Rules: <span class="copy-text">{{ $drug->rule }}</span></span>
                                                <span class="time">Time: <span class="copy-text">{{ $drug->time }}</span></span>
                                                <span class="duration">Duration: <span class="copy-text">{{ $drug->duration }} Days</span></span>
                                            </div>
                                            @if($drug->note)
                                                <div class="medicine-note">Note: <span class="copy-text">{{ $drug->note }}</span></div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="prescription-line">No prescribed drugs found.</div>
                                @endforelse


                            </div>
                            <div class="signature-note">
                                Signature
                            </div>
                            <div class="footer">
                                <div class="footer-title">{{ \App\Models\Setting::get('prescription_footer_one') }}</div>
                                <div class="footer-subtitle">{{ \App\Models\Setting::get('prescription_footer_two') }}</div>
                            </div>
                        </div>
                    </div>


                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-end mt-4 px-4 pb-4">
                    <button class="btn btn-success" onclick="window.print();">Print Prescription</button>
                </div>
            </div>
        </div>
    </div>



    @push('scripts')

    @endpush




@endsection
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

@push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;600;700&display=swap');

        .prescription-container {
            background-color: white;
            border: 1px solid #e5e7eb;
        }

        .prescription-header {
            background-color: #fffde6;
            position: relative;
            padding: 1rem;
        }

        .specialization-banner {
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            background-color: #006838;
            color: white;
            padding: 0.45rem 0.5rem;
            text-align: center;
            font-size: 0.92rem;
            letter-spacing: 0.025em;
            line-height: 1.5;
            border-bottom-left-radius: 45px;
            border-bottom-right-radius: 45px;
            margin: 0;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            gap: 4rem;
            margin-top: 4.25rem;
            padding: 0 1rem;
        }
        .header-right {
            text-align: left;
            margin: 0 auto;
        }

        .doctor-name {
            font-size: 1.2rem;
            font-weight: bold;
            color: #006838;
            margin-bottom: 0.375rem;
            letter-spacing: 0.01em;
        }

        .qualifications {
            font-size: 0.875rem;
            color: #006838;
            line-height: 1.4;
        }

        .qualifications p {
            margin-bottom: 0.125rem;
        }

        .patient-info {
            display: flex;
            justify-content: space-between;
            font-size: 0.875rem;
            color: #4b5563;
            border-top: 1px solid #e5e7eb;
            margin-top: 1rem;
            padding-top: 0.5rem;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .info-field {
            flex: 1;
            min-width: 180px;
            margin: 0 0.5rem;
            display: flex;
            align-items: center;
        }

        @media (max-width: 768px) {
            .info-field {
                min-width: 45%;
            }
        }

        .info-field span:first-child {
            width: 3rem;
            flex-shrink: 0;
        }

        .dotted-line {
            border-bottom: 1px dotted #9ca3af;
            flex: 1;
            min-width: 150px;
            height: 1.75rem;
            margin-top: 2px;
            padding: 0.25rem 0;
        }

        .main-content {
            display: flex;
            min-height: 600px;
        }

        .sidebar {
            width: 14rem;
            background-color: #fffde6;
            padding: 1rem;
            border-right: 1px solid #e5e7eb;
            font-size: 0.875rem;
        }

        .sidebar-section {
            margin-bottom: 1rem;
        }

        .section-title {
            color: #006838;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            padding-bottom: 0.25rem;
            border-bottom: 2px solid #006838;
        }

        .diagnosis-text, .investigation-text, .advice-text {
            padding: 0.5rem 0;
        }

        .diagnosis-item, .investigation-item, .advice-item {
            margin-bottom: 0.5rem;
            padding-left: 0.5rem;
            line-height: 1.4;
            color: #4b5563;
        }

        .text-muted {
            color: #6b7280;
            font-style: italic;
        }

        .prescription-area {
            flex: 1;
            padding: 1rem 1.5rem;
            position: relative;
        }

        .rx-symbol {
            font-family: serif;
            font-style: italic;
            font-weight: bold;
            font-size: 3.25rem;
            color: #006838;
            margin-bottom: 2.5rem;
            display: inline-block;
            transform: rotate(-6deg);
            text-shadow: 2px 2px 3px rgba(0,104,56,0.15);
            position: relative;
            left: -4px;
        }

        .prescription-lines {
            display: flex;
            flex-direction: column;
            gap: 1.75rem;
        }

        .prescription-line {
            border-bottom: 1px dotted #9ca3af;
            padding: 0.5rem 0;
            min-height: 1.75rem;
        }

        .medicine-details {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .medicine-number {
            color: #006838;
            margin-right: 0.5rem;
            font-size: 1rem;
        }

        .medicine-name {
            font-weight: 600;
            color: #1f2937;
            min-width: 200px;
        }

        .medicine-instructions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            align-items: center;
            padding: 0.25rem 0;
            color: #4b5563;
            font-size: 0.9rem;
        }

        .rule, .time, .duration {
            color: #4b5563;
            font-size: 0.9rem;
        }

        .medicine-note {
            margin-left: 1.5rem;
            font-size: 0.85rem;
            color: #6b7280;
            font-style: italic;
        }

        .footer {
            display: block !important;
            background-color: #006838 !important;
            color: white !important;
            padding: 0.35rem !important;
            text-align: center !important;
        }

        .footer-title {
            font-size: 1.125rem;
            font-weight: 600;
        }

        .footer-subtitle {
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .signature-note {
            text-align: center;
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 1.5rem;
            padding-top: 1rem;
        }

        @media print {
            @page {
                size: A4;
                margin: 0;
            }

            body {
                visibility: hidden;
                margin: 0;
                padding: 0;

            }

            .container-fluid {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: none !important;
            }

            .card {
                margin: 0 !important;
                border: none !important;
                box-shadow: none !important;
            }

            .card-body {
                padding: 0 !important;
            }


            .prescription-container {
                visibility: visible;
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                background-color: white !important;
                color-adjust: exact !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .prescription-container * {
                visibility: visible;
            }
            .prescription-content {
                flex: 1 0 auto;
            }

            .prescription-header {
                background-color: #fffde6 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            /* .patient-info {
                flex-wrap: nowrap !important;
                gap: 0 !important;


            } */

            .patient-info {
                display: flex;
                justify-content: space-between;
                font-size: 0.875rem;
                color: #4b5563;
                border-top: 1px solid #e5e7eb;
                margin-top: 1rem;
                padding-top: 0.5rem;
                flex-wrap: wrap;
                gap: 0.75rem;
            }

            .info-field {
                flex: 1;
                min-width: 180px;
                margin: 0 0.005rem;
                display: flex;
                align-items: center;
            }






            .specialization-banner {
                background-color: #006838 !important;
                color: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .sidebar {
                background-color: #fffde6 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .footer {
                background-color: #006838 !important;
                color: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .btn {
                display: none !important;
            }
        }
    </style>
@endpush
