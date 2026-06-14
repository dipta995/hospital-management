@extends('backend.layouts.master')

@section('title')
    Prescription — {{ $prescription->invoice->patient_name ?? 'View' }}
@endsection

@push('styles')
    @include('backend.layouts.partials.crud-styles')
    <style>
        .rx-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .prescription-container {
            background-color: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }

        .prescription-header {
            background-color: #fffde6;
            position: relative;
            padding: 1rem;
        }

        .header-content {
            display: flex;
            justify-content: center;
            padding: 0 1rem;
        }

        .header-right { text-align: center; }

        .doctor-name {
            font-size: 1.2rem;
            font-weight: bold;
            color: #006838;
            margin-bottom: 0.375rem;
        }

        .qualifications {
            font-size: 0.875rem;
            color: #006838;
            line-height: 1.4;
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
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .dotted-line {
            border-bottom: 1px dotted #9ca3af;
            flex: 1;
            min-width: 120px;
            padding: 0.25rem 0;
        }

        .main-content {
            display: flex;
            min-height: 520px;
        }

        .sidebar {
            width: 14rem;
            background-color: #fffde6;
            padding: 1rem;
            border-right: 1px solid #e5e7eb;
            font-size: 0.875rem;
        }

        .section-title {
            color: #006838;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            padding-bottom: 0.25rem;
            border-bottom: 2px solid #006838;
        }

        .sidebar-section { margin-bottom: 1rem; }

        .prescription-area {
            flex: 1;
            padding: 1rem 1.5rem;
            position: relative;
        }

        .rx-symbol {
            font-family: serif;
            font-style: italic;
            font-weight: bold;
            font-size: 3rem;
            color: #006838;
            margin-bottom: 2rem;
            transform: rotate(-6deg);
        }

        .prescription-lines { display: flex; flex-direction: column; gap: 1.25rem; }

        .prescription-line {
            border-bottom: 1px dotted #9ca3af;
            padding: 0.5rem 0;
        }

        .medicine-instructions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            align-items: center;
            font-size: 0.9rem;
        }

        .medicine-number { color: #006838; font-weight: 700; }

        .footer {
            background-color: #006838;
            color: white;
            padding: 0.35rem;
            text-align: center;
        }

        .signature-note {
            text-align: center;
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 1.5rem;
        }

        @media (max-width: 768px) {
            .main-content { flex-direction: column; }
            .sidebar { width: 100%; border-right: none; border-bottom: 1px solid #e5e7eb; }
        }

        @media print {
            @page { size: A4; margin: 0; }
            body { visibility: hidden; margin: 0; padding: 0; }
            .rx-no-print { display: none !important; }
            .prescription-container {
                visibility: visible;
                position: absolute;
                left: 0; top: 0;
                width: 100%;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .prescription-container * { visibility: visible; }
        }
    </style>
@endpush

@section('admin-content')
    <div class="crud-page container-fluid py-3">
        <div class="rx-toolbar rx-no-print">
            <div>
                <h1 class="h4 mb-1">Prescription</h1>
                <p class="text-muted mb-0">{{ $prescription->invoice->patient_name ?? 'N/A' }} · {{ $prescription->created_at->format('d M Y') }}</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route($pageHeader['index_route']) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                @if(auth('admin')->user()?->can('prescriptions.edit'))
                    <a href="{{ route($pageHeader['edit_route'], $prescription->id) }}" class="btn btn-outline-primary">
                        <i class="fas fa-pen"></i> Edit
                    </a>
                @endif
                <button type="button" class="btn btn-success" onclick="window.print();">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>

        <div id="copyContent" class="prescription-container">
            <div class="prescription-header">
                <div class="header-content">
                    <div class="header-right">
                        <div class="doctor-name">{{ $prescription->doctor->name ?? 'N/A' }}</div>
                        <div class="qualifications">{!! $prescription->doctor->designation ?? '' !!}</div>
                        <p class="mb-0"><strong>Issued:</strong> {{ $prescription->created_at->format('d M Y') }}</p>
                    </div>
                </div>

                <div class="patient-info">
                    <div class="info-field">
                        <span>Name:</span>
                        <span class="dotted-line">{{ $prescription->invoice->patient_name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-field">
                        <span>Age:</span>
                        <span class="dotted-line">{{ $prescription->invoice->patient_age_year ?? 'N/A' }}</span>
                    </div>
                    <div class="info-field">
                        <span>Date:</span>
                        <span class="dotted-line">{{ $prescription->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="info-field">
                        <span>Sex:</span>
                        <span class="dotted-line">{{ $prescription->invoice->patient_gender ?? 'N/A' }}</span>
                    </div>
                    <div class="info-field">
                        <span>Blood:</span>
                        <span class="dotted-line">{{ $prescription->invoice->patient_blood_group ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div class="main-content">
                <div class="sidebar">
                    <div class="sidebar-section">
                        <h5 class="section-title">(C/C)</h5>
                        @if($prescription->diagnosis)
                            @foreach(explode("\n", $prescription->diagnosis) as $line)
                                @if(trim($line) !== '')<div>{{ $line }}</div>@endif
                            @endforeach
                        @else
                            <div class="text-muted">No complaints recorded</div>
                        @endif
                    </div>

                    <div class="sidebar-section">
                        <h5 class="section-title">(O/E)</h5>
                        @if($prescription->investigation)
                            @foreach(explode("\n", $prescription->investigation) as $line)
                                @if(trim($line) !== '')<div>{{ $line }}</div>@endif
                            @endforeach
                        @else
                            <div class="text-muted">No examination notes</div>
                        @endif
                    </div>

                    <div class="sidebar-section">
                        <h5 class="section-title">Medical Advice</h5>
                        @forelse($tests as $item)
                            <div>{{ $item->product->name ?? 'Test' }}</div>
                        @empty
                            <div class="text-muted">No tests advised</div>
                        @endforelse
                    </div>
                </div>

                <div class="prescription-area">
                    <div class="rx-symbol">℞</div>
                    <div class="prescription-lines">
                        @forelse($prescription->drugs as $index => $drug)
                            <div class="prescription-line">
                                <div class="medicine-instructions">
                                    <span class="medicine-number">{{ $index + 1 }}.</span>
                                    <span><strong>{{ $drug->name }}</strong></span>
                                    @if($drug->rule)<span>Rule: {{ $drug->rule }}</span>@endif
                                    @if($drug->time)<span>Time: {{ $drug->time }}</span>@endif
                                    @if($drug->duration)<span>Duration: {{ $drug->duration }}</span>@endif
                                </div>
                                @if($drug->note)
                                    <div class="text-muted small ms-4">Note: {{ $drug->note }}</div>
                                @endif
                            </div>
                        @empty
                            <div class="prescription-line text-muted">No prescribed drugs.</div>
                        @endforelse
                    </div>
                    <div class="signature-note">Signature</div>
                    <div class="footer">
                        <div class="footer-title">{{ \App\Models\Setting::get('prescription_footer_one') }}</div>
                        <div class="footer-subtitle">{{ \App\Models\Setting::get('prescription_footer_two') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
