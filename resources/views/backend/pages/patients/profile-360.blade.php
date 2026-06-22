@extends('backend.layouts.master')
@section('title')
    Patient 360 — {{ $profile['patient']['name'] ?? 'Profile' }}
@endsection

@push('styles')
    @include('backend.layouts.partials.invoice-styles')
    <style>
        .p360-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e40af 60%, #0f766e 100%);
            border-radius: 16px;
            padding: 24px;
            color: #fff;
            margin-bottom: 20px;
        }

        .p360-segment {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
        }

        .p360-grid {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 20px;
        }

        @media (max-width: 1100px) {
            .p360-grid { grid-template-columns: 1fr; }
        }

        .p360-stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 12px;
            margin-top: 16px;
        }

        .p360-stat {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 12px;
            padding: 12px 14px;
        }

        .p360-stat-label { font-size: 0.72rem; opacity: 0.85; text-transform: uppercase; }
        .p360-stat-value { font-size: 1.25rem; font-weight: 800; }

        .p360-predict {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 20px;
        }

        .p360-timeline-item {
            display: flex;
            gap: 14px;
            padding: 14px 0;
            border-bottom: 1px solid var(--inv-border);
        }

        .p360-timeline-item:last-child { border-bottom: none; }

        .p360-timeline-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .dash-patient-badge.special { background: #fef3c7; color: #b45309; }
        .dash-patient-badge.regular { background: #d1fae5; color: #047857; }
        .dash-patient-badge.new { background: #dbeafe; color: #1d4ed8; }
        .dash-patient-badge.returning { background: #cffafe; color: #0e7490; }
        .dash-patient-badge.occasional { background: #e2e8f0; color: #475569; }
        .dash-patient-badge.at_risk { background: #ffedd5; color: #c2410c; }
    </style>
@endpush

@section('admin-content')
    @php
        $fmt = fn ($n) => number_format((float) $n, 2);
        $p = $profile['patient'];
        $stats = $profile['stats'];
        $due = $profile['due'];
        $modules = $profile['modules'];
    @endphp

    <div class="inv-page container-fluid py-3">
        <div class="p360-hero">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <div class="mb-2">
                        <span class="p360-segment dash-patient-badge {{ $profile['segment'] }}">
                            <i class="fas fa-tag"></i> {{ $profile['segment_label'] }}
                        </span>
                    </div>
                    <h1 class="h3 fw-bold mb-1">{{ $p['name'] }}</h1>
                    <div class="opacity-90">
                        <i class="fas fa-phone me-1"></i> {{ $p['phone'] }}
                        @if(!empty($p['age'])) · {{ $p['age'] }} yrs @endif
                        @if(!empty($p['gender'])) · {{ ucfirst($p['gender']) }} @endif
                        @if(!empty($p['blood_group'])) · {{ $p['blood_group'] }} @endif
                    </div>
                    @if(!empty($p['address']))
                        <div class="small opacity-75 mt-1"><i class="fas fa-map-marker-alt me-1"></i> {{ $p['address'] }}</div>
                    @endif
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.users.index') }}" class="inv-btn-glass"><i class="fas fa-arrow-left"></i> Patients</a>
                    @if($userGuard = Auth::guard('admin')->user())
                        @if($userGuard->can('invoices.create'))
                            <a href="{{ route('admin.invoices.create') }}" class="inv-btn-white"><i class="fas fa-plus"></i> New Invoice</a>
                        @endif
                    @endif
                </div>
            </div>
            <div class="p360-stat-grid">
                <div class="p360-stat">
                    <div class="p360-stat-label">Total Visits</div>
                    <div class="p360-stat-value">{{ $stats['visit_count'] }}</div>
                </div>
                <div class="p360-stat">
                    <div class="p360-stat-label">Lifetime Spent</div>
                    <div class="p360-stat-value">৳ {{ $fmt($stats['total_spent']) }}</div>
                </div>
                <div class="p360-stat">
                    <div class="p360-stat-label">First Visit</div>
                    <div class="p360-stat-value" style="font-size:1rem;">{{ $stats['first_visit'] }}</div>
                </div>
                <div class="p360-stat">
                    <div class="p360-stat-label">Last Visit</div>
                    <div class="p360-stat-value" style="font-size:1rem;">{{ $stats['last_visit'] }}</div>
                </div>
                <div class="p360-stat">
                    <div class="p360-stat-label">Outstanding Due</div>
                    <div class="p360-stat-value {{ $due['total'] > 0 ? 'text-warning' : '' }}">৳ {{ $fmt($due['total']) }}</div>
                </div>
            </div>
        </div>

        <div class="p360-predict">
            <strong><i class="fas fa-lightbulb text-warning me-1"></i> Prediction & Action</strong>
            <div class="text-muted mt-1">{{ $profile['prediction'] }}</div>
        </div>

        @if(Auth::guard('admin')->user()?->can('ai.health'))
        <div class="inv-panel mb-3" id="p360-ai-health-panel">
            <div class="inv-panel-head d-flex justify-content-between align-items-center flex-wrap gap-2" style="cursor: default;">
                <h6 class="mb-0"><i class="fas fa-notes-medical me-2 text-primary"></i> {{ t('ai.health_explanation') }}</h6>
                <button type="button" class="btn btn-sm btn-dark" id="p360-ai-explain-btn">
                    {{ t('ai.generate') }}
                </button>
            </div>
            <div class="p-3">
                <div id="p360-ai-risk-meter" class="d-none mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-semibold">{{ t('ai.risk_score') }}</span>
                        <span id="p360-ai-risk-label" class="badge bg-secondary">—</span>
                    </div>
                    <div class="progress" style="height:8px;">
                        <div id="p360-ai-risk-bar" class="progress-bar" style="width:0%"></div>
                    </div>
                </div>
                <div id="p360-ai-factors" class="mb-3 d-none"></div>
                <div id="p360-ai-care-plan" class="mb-3 d-none"></div>
                <div id="p360-ai-health-loading" class="text-muted small d-none"><i class="fas fa-spinner fa-spin"></i></div>
                <div id="p360-ai-health-content" class="text-muted small">—</div>
            </div>
        </div>
        @endif

        <div class="p360-grid">
            <div>
                <div class="inv-panel mb-3">
                    <div class="inv-panel-head" style="cursor: default;">
                        <h6><i class="fas fa-history me-2 text-primary"></i> Full Visit History</h6>
                    </div>
                    <div class="p-3">
                        @forelse($profile['timeline'] as $event)
                            <div class="p360-timeline-item">
                                <div class="p360-timeline-icon"><i class="fas {{ $event['icon'] }}"></i></div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $event['label'] }}</div>
                                    <div class="small text-muted">{{ $event['meta'] }}</div>
                                    <div class="small text-muted">{{ $event['date'] }}</div>
                                </div>
                                <div class="text-end">
                                    @if($event['amount'] > 0)
                                        <div class="fw-bold">৳ {{ $fmt($event['amount']) }}</div>
                                    @endif
                                    @if($event['type'] === 'invoice' && !empty($event['entity_id']))
                                        <a href="{{ route('admin.invoices.show', $event['entity_id']) }}" class="btn btn-sm btn-outline-primary mt-1">View</a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0 text-center py-4">No visit history found yet.</p>
                        @endforelse
                    </div>
                </div>

                @if(count($due['invoices'] ?? []) > 0)
                    <div class="inv-panel">
                        <div class="inv-panel-head" style="cursor: default;">
                            <h6><i class="fas fa-file-invoice-dollar me-2 text-danger"></i> Due Invoices</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="inv-table table mb-0">
                                <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Date</th>
                                    <th class="text-end">Due</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($due['invoices'] as $inv)
                                    <tr>
                                        <td>{{ $inv['invoice_number'] }}</td>
                                        <td>{{ $inv['date'] }}</td>
                                        <td class="text-end fw-bold text-danger">৳ {{ $fmt($inv['due']) }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.invoices.show', $inv['invoice_id']) }}" class="btn btn-sm btn-outline-primary">Open</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            <div>
                <div class="inv-panel mb-3">
                    <div class="inv-panel-head" style="cursor: default;">
                        <h6><i class="fas fa-th-large me-2 text-primary"></i> Module Breakdown</h6>
                    </div>
                    <div class="p-3">
                        <div class="dash-breakdown">
                            <span class="dash-pill inv">Invoices {{ $modules['invoices'] }}</span>
                            <span class="dash-pill rec">OPD {{ $modules['opd'] }}</span>
                            <span class="dash-pill earn">Recepts {{ $modules['recepts'] }}</span>
                            <span class="dash-pill cost">Pharmacy {{ $modules['pharmacy'] }}</span>
                            <span class="dash-pill net">Admits {{ $modules['admits'] }}</span>
                        </div>
                        @if($stats['active_admit'])
                            <div class="alert alert-success mt-3 mb-0 py-2 small">
                                <i class="fas fa-procedures me-1"></i> Currently admitted (IPD)
                            </div>
                        @endif
                    </div>
                </div>

                <div class="inv-panel">
                    <div class="inv-panel-head" style="cursor: default;">
                        <h6><i class="fas fa-info-circle me-2 text-primary"></i> Segment Guide</h6>
                    </div>
                    <div class="p-3 small text-muted">
                        @foreach($segmentLabels as $key => $label)
                            <div class="mb-1"><span class="dash-patient-badge {{ $key }}">{{ $label }}</span></div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@if(Auth::guard('admin')->user()?->can('ai.health'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    var btn = document.getElementById('p360-ai-explain-btn');
    var content = document.getElementById('p360-ai-health-content');
    var loading = document.getElementById('p360-ai-health-loading');
    var riskMeter = document.getElementById('p360-ai-risk-meter');
    var riskBar = document.getElementById('p360-ai-risk-bar');
    var riskLabel = document.getElementById('p360-ai-risk-label');
    var factorsEl = document.getElementById('p360-ai-factors');
    var careEl = document.getElementById('p360-ai-care-plan');
    var url = @json(route('admin.ai.health-explain')) + '?' + @json(http_build_query(array_filter(['phone' => request('phone'), 'user_id' => request('user_id')])));
    var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    var labels = {
        care: @json(t('ai.care_plan')),
        riskHigh: @json(t('ai.risk_high')),
        riskModerate: @json(t('ai.risk_moderate')),
        riskLow: @json(t('ai.risk_low')),
    };

    var riskClass = function (level) {
        if (level === 'high') return { bar: 'bg-danger', badge: 'bg-danger', text: labels.riskHigh };
        if (level === 'moderate') return { bar: 'bg-warning', badge: 'bg-warning text-dark', text: labels.riskModerate };
        return { bar: 'bg-success', badge: 'bg-success', text: labels.riskLow };
    };

    btn?.addEventListener('click', function () {
        if (loading) loading.classList.remove('d-none');
        btn.disabled = true;
        fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.risk_score != null && riskMeter && riskBar && riskLabel) {
                    riskMeter.classList.remove('d-none');
                    var rc = riskClass(data.risk_level);
                    riskBar.style.width = data.risk_score + '%';
                    riskBar.className = 'progress-bar ' + rc.bar;
                    riskLabel.className = 'badge ' + rc.badge;
                    riskLabel.textContent = data.risk_score + '/100 — ' + rc.text;
                }
                if (factorsEl && data.factors && data.factors.length) {
                    factorsEl.classList.remove('d-none');
                    factorsEl.innerHTML = data.factors.map(function (f) {
                        return '<div class="alert alert-light border py-2 px-3 small mb-2">' +
                            '<span class="badge bg-secondary me-1">' + f.impact + '</span> ' +
                            '<strong>' + f.label + '</strong> — ' + f.detail + '</div>';
                    }).join('');
                }
                if (careEl && data.care_plan && data.care_plan.length) {
                    careEl.classList.remove('d-none');
                    careEl.innerHTML = '<div class="small fw-semibold mb-2">' + labels.care + '</div><ul class="small mb-0">' +
                        data.care_plan.map(function (s) { return '<li>' + s + '</li>'; }).join('') + '</ul>';
                }
                if (content && data.content) {
                    content.textContent = data.content;
                    content.classList.remove('text-muted', 'small');
                    content.style.lineHeight = '1.65';
                    content.style.fontSize = '0.9rem';
                    content.style.color = '#334155';
                }
            })
            .catch(function () {
                if (content) content.textContent = @json(t('ai.request_failed'));
            })
            .finally(function () {
                if (loading) loading.classList.add('d-none');
                btn.disabled = false;
            });
    });
});
</script>
@endif
@endpush
