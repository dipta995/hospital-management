@extends('backend.layouts.master')
@section('title')
    {{ t('dashboard.title') }}
@endsection

@push('styles')
    @include('backend.layouts.partials.invoice-styles')
    <style>
        .dash-ops-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }

        .dash-op {
            background: var(--inv-surface);
            border: 1px solid var(--inv-border);
            border-radius: 14px;
            padding: 16px 18px;
            text-decoration: none;
            color: inherit;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            display: block;
        }

        .dash-op:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(15,23,42,0.08);
            color: inherit;
        }

        .dash-op-label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--inv-muted);
            margin-bottom: 4px;
        }

        .dash-op-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }

        .dash-op-sub {
            font-size: 0.8rem;
            color: var(--inv-muted);
            margin-top: 4px;
        }

        .dash-op-icon {
            float: right;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
        }

        .dash-op-icon.blue { background: #eff6ff; color: #2563eb; }
        .dash-op-icon.green { background: #ecfdf5; color: #059669; }
        .dash-op-icon.amber { background: #fffbeb; color: #d97706; }
        .dash-op-icon.rose { background: #fdf2f8; color: #db2777; }
        .dash-op-icon.cyan { background: #ecfeff; color: #0891b2; }
        .dash-op-icon.slate { background: #f1f5f9; color: #475569; }

        .dash-period-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 14px;
            margin-bottom: 20px;
        }

        .dash-period {
            background: var(--inv-surface);
            border: 1px solid var(--inv-border);
            border-radius: 14px;
            padding: 18px 20px;
            box-shadow: 0 4px 16px rgba(15,23,42,0.04);
        }

        .dash-period-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .dash-period-title {
            font-size: 0.82rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--inv-muted);
        }

        .dash-period-total {
            font-size: 1.45rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 10px;
        }

        .dash-period-total.negative { color: var(--inv-danger); }

        .dash-breakdown {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .dash-pill {
            font-size: 0.72rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 999px;
            background: #f1f5f9;
            color: #475569;
        }

        .dash-pill.inv { background: #eff6ff; color: #1d4ed8; }
        .dash-pill.rec { background: #ecfdf5; color: #047857; }
        .dash-pill.earn { background: #fef3c7; color: #b45309; }
        .dash-pill.pharm { background: #f5f3ff; color: #6d28d9; }
        .dash-pill.cost { background: #fef2f2; color: #b91c1c; }
        .dash-pill.net { background: #f0fdf4; color: #15803d; }

        .dash-layout {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 20px;
            margin-bottom: 20px;
        }

        @media (max-width: 1100px) {
            .dash-layout { grid-template-columns: 1fr; }
        }

        #dash-week-chart { min-height: 320px; }

        .dash-today-highlight {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border-color: #6ee7b7;
        }

        .dash-today-highlight .dash-period-total { color: #047857; }

        .inv-kpi-icon.net { background: #f0fdf4; color: #15803d; }
        .inv-kpi-icon.week { background: #eff6ff; color: #2563eb; }
        .inv-kpi-icon.month { background: #faf5ff; color: #7c3aed; }
        .inv-kpi-icon.cost { background: #fef2f2; color: #dc2626; }

        .dash-due-badge {
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            background: #fef2f2;
            color: #b91c1c;
        }

        .dash-due-badge.paid {
            background: #ecfdf5;
            color: #047857;
        }

        .dash-hr-card {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-radius: var(--inv-radius);
            padding: 20px 24px;
            margin-bottom: 20px;
        }

        .dash-schema-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 12px;
        }

        .dash-schema-module {
            background: var(--inv-surface);
            border: 1px solid var(--inv-border);
            border-radius: 12px;
            padding: 14px 16px;
        }

        .dash-schema-module.installed {
            border-color: #bbf7d0;
        }

        .dash-schema-status {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            list-style: none;
            padding: 0;
            margin: 0 0 10px;
        }

        .dash-schema-status li {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 6px;
            background: #f1f5f9;
            color: #64748b;
        }

        .dash-schema-status li.ok {
            background: #ecfdf5;
            color: #047857;
        }

        .dash-ai-content {
            white-space: pre-wrap;
            font-size: 0.9rem;
            line-height: 1.65;
            color: #334155;
        }

        .dash-ai-empty {
            color: #94a3b8;
            font-size: 0.88rem;
        }

        .dash-trend {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 999px;
            margin-top: 6px;
        }

        .dash-trend.up { background: #ecfdf5; color: #047857; }
        .dash-trend.down { background: #fef2f2; color: #b91c1c; }
        .dash-trend.flat { background: #f1f5f9; color: #64748b; }

        .dash-alert-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }

        .dash-alert {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid var(--inv-border);
            background: var(--inv-surface);
            text-decoration: none;
            color: inherit;
            transition: box-shadow 0.15s ease;
        }

        .dash-alert:hover { box-shadow: 0 6px 20px rgba(15,23,42,0.08); color: inherit; }
        .dash-alert.warn { border-color: #fcd34d; background: #fffbeb; }
        .dash-alert.danger { border-color: #fecaca; background: #fef2f2; }
        .dash-alert.info { border-color: #bae6fd; background: #f0f9ff; }

        .dash-alert-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: #fff;
        }

        .dash-quick-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 10px;
            margin-bottom: 20px;
        }

        .dash-quick {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 14px 10px;
            border-radius: 12px;
            border: 1px solid var(--inv-border);
            background: var(--inv-surface);
            text-decoration: none;
            color: #334155;
            font-size: 0.82rem;
            font-weight: 600;
            text-align: center;
            transition: all 0.15s ease;
        }

        .dash-quick:hover {
            border-color: #93c5fd;
            background: #eff6ff;
            color: #1d4ed8;
            transform: translateY(-1px);
        }

        .dash-quick i { font-size: 1.2rem; color: #2563eb; }

        .dash-split-layout {
            display: grid;
            grid-template-columns: 1fr 280px;
            gap: 20px;
        }

        @media (max-width: 900px) {
            .dash-split-layout { grid-template-columns: 1fr; }
        }

        #dash-today-split { min-height: 280px; }

        /* Live realtime bar */
        .dash-live-bar {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 55%, #0f766e 100%);
            border-radius: 16px;
            padding: 20px 24px;
            margin-bottom: 20px;
            color: #fff;
            box-shadow: 0 12px 40px rgba(15, 23, 42, 0.25);
            position: relative;
            overflow: hidden;
        }

        .dash-live-bar::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 85% 20%, rgba(255,255,255,0.12) 0%, transparent 45%);
            pointer-events: none;
        }

        .dash-live-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 16px;
            position: relative;
        }

        .dash-live-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            background: rgba(255,255,255,0.12);
            padding: 6px 12px;
            border-radius: 999px;
        }

        .dash-live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #4ade80;
            box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.7);
            animation: dash-live-pulse 2s infinite;
        }

        @keyframes dash-live-pulse {
            0% { box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(74, 222, 128, 0); }
            100% { box-shadow: 0 0 0 0 rgba(74, 222, 128, 0); }
        }

        .dash-live-updated {
            font-size: 0.82rem;
            opacity: 0.85;
        }

        .dash-live-main {
            display: flex;
            align-items: flex-end;
            gap: 16px;
            flex-wrap: wrap;
            position: relative;
        }

        .dash-live-total-wrap {
            flex: 0 0 auto;
        }

        .dash-live-total-label {
            font-size: 0.85rem;
            font-weight: 600;
            opacity: 0.9;
            margin-bottom: 4px;
        }

        .dash-live-total {
            font-size: clamp(2.5rem, 5vw, 3.5rem);
            font-weight: 900;
            line-height: 1;
            letter-spacing: -0.02em;
            transition: transform 0.25s ease, color 0.25s ease;
        }

        .dash-live-total.flash-up { animation: dash-num-flash-up 0.6s ease; }
        .dash-live-total.flash-down { animation: dash-num-flash-down 0.6s ease; }

        @keyframes dash-num-flash-up {
            0% { transform: scale(1); color: #fff; }
            40% { transform: scale(1.08); color: #86efac; }
            100% { transform: scale(1); color: #fff; }
        }

        @keyframes dash-num-flash-down {
            0% { transform: scale(1); color: #fff; }
            40% { transform: scale(1.05); color: #fca5a5; }
            100% { transform: scale(1); color: #fff; }
        }

        .dash-live-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            flex: 1;
            min-width: 200px;
        }

        .dash-live-chip {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 12px;
            padding: 10px 14px;
            min-width: 120px;
        }

        .dash-live-chip-label {
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            opacity: 0.8;
            margin-bottom: 2px;
        }

        .dash-live-chip-value {
            font-size: 1.35rem;
            font-weight: 800;
            line-height: 1.2;
        }

        .dash-live-chip-sub {
            font-size: 0.72rem;
            opacity: 0.75;
            margin-top: 2px;
        }

        .dash-live-footfall {
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px solid rgba(255,255,255,0.15);
            font-size: 0.88rem;
            opacity: 0.92;
            position: relative;
        }

        [data-live].live-changed {
            animation: dash-cell-flash 0.8s ease;
        }

        #dash-live-root.dash-syncing {
            transition: opacity 0.2s ease;
        }

        #dash-live-root.dash-sync-flash {
            animation: dash-root-flash 0.35s ease;
        }

        @keyframes dash-root-flash {
            0% { opacity: 1; }
            50% { opacity: 0.97; }
            100% { opacity: 1; }
        }

        @keyframes dash-cell-flash {
            0% { background-color: transparent; }
            30% { background-color: rgba(37, 99, 235, 0.12); border-radius: 6px; }
            100% { background-color: transparent; }
        }

        /* Patient intelligence */
        .dash-patient-grid {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 20px;
            margin-bottom: 20px;
        }

        @media (max-width: 1000px) {
            .dash-patient-grid { grid-template-columns: 1fr; }
        }

        .dash-segment-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 16px;
        }

        .dash-segment-chip {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 12px;
            border: 1px solid var(--inv-border);
            background: var(--inv-surface);
            min-width: 130px;
        }

        .dash-segment-chip .count {
            font-size: 1.35rem;
            font-weight: 800;
            line-height: 1;
        }

        .dash-segment-chip .label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--inv-muted);
        }

        .dash-segment-chip.special { border-color: #fcd34d; background: #fffbeb; }
        .dash-segment-chip.regular { border-color: #6ee7b7; background: #ecfdf5; }
        .dash-segment-chip.new { border-color: #93c5fd; background: #eff6ff; }
        .dash-segment-chip.returning { border-color: #67e8f9; background: #ecfeff; }
        .dash-segment-chip.occasional { border-color: #cbd5e1; background: #f8fafc; }
        .dash-segment-chip.at_risk { border-color: #fdba74; background: #fff7ed; }

        .dash-patient-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 4px 8px;
            border-radius: 999px;
            white-space: nowrap;
        }

        .dash-patient-badge.special { background: #fef3c7; color: #b45309; }
        .dash-patient-badge.regular { background: #d1fae5; color: #047857; }
        .dash-patient-badge.new { background: #dbeafe; color: #1d4ed8; }
        .dash-patient-badge.returning { background: #cffafe; color: #0e7490; }
        .dash-patient-badge.occasional { background: #e2e8f0; color: #475569; }
        .dash-patient-badge.at_risk { background: #ffedd5; color: #c2410c; }

        .dash-predict-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 10px;
            margin-bottom: 16px;
        }

        .dash-predict {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid var(--inv-border);
            background: #f8fafc;
        }

        .dash-predict.info { background: #eff6ff; border-color: #bfdbfe; }
        .dash-predict.warn { background: #fff7ed; border-color: #fed7aa; }
        .dash-predict.success { background: #ecfdf5; border-color: #a7f3d0; }
        .dash-predict.special { background: #fffbeb; border-color: #fde68a; }

        .dash-predict-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        #dash-patient-segment-chart { min-height: 260px; }
    </style>
@endpush

@section('admin-content')
    @php
        $userGuard = Auth::guard('admin')->user();
        $fmt = fn ($n) => number_format((float) $n, 2);
        $d = fn ($key, $replace = []) => t('dashboard.'.$key, $replace);
    @endphp

    <div class="inv-page container-fluid py-3">

        {{-- Hero --}}
        <div class="inv-hero">
            <div class="inv-hero-inner">
                <div class="inv-hero-left">
                    <div class="inv-hero-icon"><i class="fas fa-chart-line"></i></div>
                    <div>
                        <h1 class="inv-hero-title">{{ $d('title') }}</h1>
                        <p class="inv-hero-sub">
                            @if(!empty($adminName))
                                {{ $d('welcome_user', ['name' => $adminName]) }} ·
                            @endif
                            {{ $todayLabel ?? now()->format('l, d M Y') }} · {{ $d('live_updates') }}
                        </p>
                    </div>
                </div>
                <div class="inv-hero-actions">
                    <span class="inv-btn-glass" id="dash-live-status" style="cursor: default;">
                        <span class="dash-live-dot d-inline-block me-1" style="vertical-align: middle;"></span>
                        {{ t('common.live') }} · <span id="dash-live-clock">—</span>
                    </span>
                    @if($userGuard && $userGuard->can('invoices.create'))
                        <a href="{{ route('admin.invoices.create') }}" class="inv-btn-white">
                            <i class="fas fa-plus"></i> {{ $d('new_invoice') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>

        @include('backend.layouts.partials.message')

        @if(!empty($canManageSystemSchema))
            <div class="inv-panel mb-3">
                <div class="inv-panel-head d-flex justify-content-between align-items-center flex-wrap gap-2" style="cursor: default;">
                    <h6 class="mb-0"><i class="fas fa-database me-2 text-secondary"></i> {{ $d('schema_maintenance') }}</h6>
                    <div class="d-flex align-items-center gap-2">
                        @if(($pendingSchemaCount ?? 0) > 0)
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                {{ $d('schema_pending_count', ['count' => $pendingSchemaCount]) }}
                            </span>
                        @endif
                        <a href="{{ route('admin.system.updates') }}" class="btn btn-sm btn-outline-dark">
                            <i class="fas fa-external-link-alt me-1"></i> {{ $d('open_schema_updates') }}
                        </a>
                    </div>
                </div>
                <div class="p-3">
                    @include('backend.layouts.partials.schema-updates-panel', [
                        'schemaModules' => $schemaModules ?? [],
                        'pendingCount' => $pendingSchemaCount ?? 0,
                        'compact' => true,
                    ])
                </div>
            </div>
        @endif

        @if ($userGuard && $userGuard->can('dashboards.view'))

            <div id="dash-live-root">

            {{-- Today KPI row --}}
            @php
                $trendClass = fn ($v) => $v === null ? 'flat' : ($v > 0 ? 'up' : ($v < 0 ? 'down' : 'flat'));
                $trendIcon = fn ($v) => $v === null ? 'fa-minus' : ($v > 0 ? 'fa-arrow-up' : ($v < 0 ? 'fa-arrow-down' : 'fa-minus'));
            @endphp

            {{-- Live patient activity (auto-updates) --}}
            <div class="dash-live-bar" id="dash-live-bar">
                <div class="dash-live-head">
                    <span class="dash-live-badge">
                        <span class="dash-live-dot"></span>
                        {{ $d('realtime') }}
                    </span>
                    <span class="dash-live-updated">{{ $d('last_updated') }} <strong id="dash-live-updated">{{ t('common.just_now') }}</strong></span>
                </div>
                <div class="dash-live-main">
                    <div class="dash-live-total-wrap">
                        <div class="dash-live-total-label">{{ $d('patients_handling_now') }}</div>
                        <div class="dash-live-total" id="dash-live-handling" data-live="patients.handling_now">
                            {{ $patients['handling_now'] ?? 0 }}
                        </div>
                    </div>
                    <div class="dash-live-chips">
                        <div class="dash-live-chip">
                            <div class="dash-live-chip-label"><i class="fas fa-user-md me-1"></i> {{ $d('opd_queue') }}</div>
                            <div class="dash-live-chip-value" data-live="patients.opd_queue">{{ $patients['opd_queue'] ?? 0 }}</div>
                            <div class="dash-live-chip-sub">
                                <span data-live="patients.opd_pending">{{ $patients['opd_pending'] ?? 0 }}</span> {{ $d('waiting') }} ·
                                <span data-live="patients.opd_checking">{{ $patients['opd_checking'] ?? 0 }}</span> {{ $d('checking') }}
                            </div>
                        </div>
                        <div class="dash-live-chip">
                            <div class="dash-live-chip-label"><i class="fas fa-procedures me-1"></i> {{ $d('ipd_admitted') }}</div>
                            <div class="dash-live-chip-value" data-live="patients.ipd_active">{{ $patients['ipd_active'] ?? 0 }}</div>
                            <div class="dash-live-chip-sub">{{ $d('currently_in_hospital') }}</div>
                        </div>
                        <div class="dash-live-chip">
                            <div class="dash-live-chip-label"><i class="fas fa-flask me-1"></i> {{ $d('lab_pending') }}</div>
                            <div class="dash-live-chip-value" data-live="patients.lab_pending">{{ $patients['lab_pending'] ?? 0 }}</div>
                            <div class="dash-live-chip-sub">{{ $d('tests_awaiting_result') }}</div>
                        </div>
                        <div class="dash-live-chip">
                            <div class="dash-live-chip-label"><i class="fas fa-hand-holding-usd me-1"></i> {{ $d('today_collection') }}</div>
                            <div class="dash-live-chip-value">৳ <span data-live="today.collection.total" data-live-fmt="money">{{ $fmt($today['collection']['total']) }}</span></div>
                            <div class="dash-live-chip-sub">{{ $d('updates_as_payments') }}</div>
                        </div>
                    </div>
                </div>
                <div class="dash-live-footfall">
                    <i class="fas fa-walking me-1"></i>
                    {{ $d('today_footfall') }}
                    <strong data-live="patients.today_footfall">{{ $patients['today_footfall'] ?? 0 }}</strong>
                    {{ $d('patients') }}
                    (<span data-live="patients.today_invoices">{{ $patients['today_invoices'] ?? 0 }}</span> {{ $d('invoices') }} ·
                    <span data-live="patients.opd_total_today">{{ $patients['opd_total_today'] ?? 0 }}</span> OPD ·
                    <span data-live="pharmacy.sales_today">{{ $pharmacy['sales_today'] ?? 0 }}</span> {{ $d('pharmacy') }})
                </div>
            </div>

            @php
                $pi = $patientInsights ?? [];
                $piToday = $pi['today_summary'] ?? [];
                $piLabels = \App\Services\PatientInsightService::segmentLabels();
            @endphp

            {{-- Patient intelligence --}}
            <div class="inv-panel mb-3" id="dash-patient-panel">
                <div class="inv-panel-head" style="cursor: default;">
                    <h6><i class="fas fa-users me-2 text-primary"></i> {{ $d('patient_intelligence') }}</h6>
                    <small class="text-muted">{{ $d('patient_intelligence_sub') }}</small>
                </div>
                <div class="p-3">
                    <div class="dash-segment-row" id="dash-segment-row">
                        @foreach($piLabels as $key => $label)
                            <div class="dash-segment-chip {{ $key }}">
                                <div>
                                    <div class="count" data-live="patientInsights.today_summary.{{ $key }}">{{ $piToday[$key] ?? 0 }}</div>
                                    <div class="label">{{ $label }} {{ $d('today') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="dash-predict-grid" id="dash-predict-grid">
                        @forelse($pi['predictions'] ?? [] as $pred)
                            <div class="dash-predict {{ $pred['tone'] ?? 'info' }}">
                                <div class="dash-predict-icon"><i class="fas {{ $pred['icon'] ?? 'fa-lightbulb' }}"></i></div>
                                <div>
                                    <strong>{{ $pred['title'] ?? '' }}</strong>
                                    <div class="small text-muted">{{ $pred['subtitle'] ?? '' }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="dash-predict info">
                                <div class="dash-predict-icon"><i class="fas fa-info-circle"></i></div>
                                <div>
                                    <strong>{{ $d('building_patterns') }}</strong>
                                    <div class="small text-muted">{{ $d('building_patterns_sub') }}</div>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <div class="dash-patient-grid">
                        <div>
                            <h6 class="mb-2 fw-bold"><i class="fas fa-user-clock me-1 text-primary"></i> {{ $d('todays_patients') }}</h6>
                            <div class="table-responsive">
                                <table class="inv-table table table-sm mb-0">
                                    <thead>
                                    <tr>
                                        <th>{{ t('common.patient') }}</th>
                                        <th>{{ $d('type') }}</th>
                                        <th class="text-end">{{ $d('visits') }}</th>
                                        <th class="text-end">{{ $d('spent') }}</th>
                                        <th>{{ $d('prediction') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody id="dash-today-patients-body">
                                    @forelse($pi['today_patients'] ?? [] as $row)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $row['name'] }}</div>
                                                <small class="text-muted">{{ $row['phone'] }}</small>
                                            </td>
                                            <td><span class="dash-patient-badge {{ $row['segment'] }}">{{ $row['segment_label'] }}</span></td>
                                            <td class="text-end">{{ $row['visit_count'] }}</td>
                                            <td class="text-end">৳ {{ $fmt($row['total_spent']) }}</td>
                                            <td><small class="text-muted">{{ $row['prediction'] }}</small></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-3">{{ $d('no_patient_activity') }}</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-2 fw-bold"><i class="fas fa-chart-pie me-1 text-primary"></i> {{ $d('todays_mix') }}</h6>
                            <div id="dash-patient-segment-chart"></div>
                            <div class="small text-muted mt-2 text-center">
                                <span data-live="patientInsights.active_patients">{{ $pi['active_patients'] ?? 0 }}</span> {{ $d('active_patients_12m') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($userGuard->can('ai.analytics'))
            <div class="inv-panel mb-3" id="dash-ai-insights-panel">
                <div class="inv-panel-head d-flex justify-content-between align-items-center flex-wrap gap-2" style="cursor: default;">
                    <h6 class="mb-0"><i class="fas fa-chart-line me-2 text-primary"></i> {{ $d('ai_business_analytics') }}</h6>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="dash-ai-refresh" title="{{ $d('ai_refresh') }}">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div class="p-3 position-relative">
                    <div id="dash-ai-insights-loading" class="text-muted small d-none">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                    <div id="dash-ai-insights-content" class="dash-ai-content dash-ai-empty">—</div>
                    <div class="small text-muted mt-2 text-end" id="dash-ai-insights-meta"></div>
                </div>
            </div>
            @endif

            <div class="inv-kpi-grid">
                <div class="inv-kpi">
                    <div class="inv-kpi-icon collection"><i class="fas fa-hand-holding-usd"></i></div>
                    <div>
                        <div class="inv-kpi-label">{{ $d('today_collection') }}</div>
                        <div class="inv-kpi-value">৳ <span data-live="today.collection.total" data-live-fmt="money">{{ $fmt($today['collection']['total']) }}</span></div>
                        @if(isset($comparisons['collection_vs_yesterday']))
                            <span class="dash-trend {{ $trendClass($comparisons['collection_vs_yesterday']) }}" id="dash-trend-collection">
                                <i class="fas {{ $trendIcon($comparisons['collection_vs_yesterday']) }}"></i>
                                {{ abs($comparisons['collection_vs_yesterday']) }}% {{ $d('vs_yesterday') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="inv-kpi">
                    <div class="inv-kpi-icon cost"><i class="fas fa-receipt"></i></div>
                    <div>
                        <div class="inv-kpi-label">{{ $d('todays_cost') }}</div>
                        <div class="inv-kpi-value">৳ <span data-live="today.cost" data-live-fmt="money">{{ $fmt($today['cost']) }}</span></div>
                    </div>
                </div>
                <div class="inv-kpi">
                    <div class="inv-kpi-icon net"><i class="fas fa-wallet"></i></div>
                    <div>
                        <div class="inv-kpi-label">{{ $d('todays_net') }}</div>
                        <div class="inv-kpi-value {{ $today['net'] < 0 ? 'text-danger' : '' }}" id="dash-kpi-net-value">
                            ৳ <span data-live="today.net" data-live-fmt="money">{{ $fmt($today['net']) }}</span>
                        </div>
                        @if(isset($comparisons['net_vs_yesterday']))
                            <span class="dash-trend {{ $trendClass($comparisons['net_vs_yesterday']) }}" id="dash-trend-net">
                                <i class="fas {{ $trendIcon($comparisons['net_vs_yesterday']) }}"></i>
                                {{ abs($comparisons['net_vs_yesterday']) }}% {{ $d('vs_yesterday') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="inv-kpi">
                    <div class="inv-kpi-icon week"><i class="fas fa-calendar-week"></i></div>
                    <div>
                        <div class="inv-kpi-label">{{ $d('this_week_net') }}</div>
                        <div class="inv-kpi-value">৳ <span data-live="thisWeek.net" data-live-fmt="money">{{ $fmt($thisWeek['net']) }}</span></div>
                        @if(isset($comparisons['net_week_vs_last_week']))
                            <span class="dash-trend {{ $trendClass($comparisons['net_week_vs_last_week']) }}" id="dash-trend-week">
                                <i class="fas {{ $trendIcon($comparisons['net_week_vs_last_week']) }}"></i>
                                {{ abs($comparisons['net_week_vs_last_week']) }}% {{ $d('vs_last_week') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="inv-kpi">
                    <div class="inv-kpi-icon month"><i class="fas fa-calendar-alt"></i></div>
                    <div>
                        <div class="inv-kpi-label">{{ $d('this_month_net') }}</div>
                        <div class="inv-kpi-value">৳ <span data-live="thisMonth.net" data-live-fmt="money">{{ $fmt($thisMonth['net']) }}</span></div>
                        @if(isset($comparisons['net_month_vs_last_month']))
                            <span class="dash-trend {{ $trendClass($comparisons['net_month_vs_last_month']) }}" id="dash-trend-month">
                                <i class="fas {{ $trendIcon($comparisons['net_month_vs_last_month']) }}"></i>
                                {{ abs($comparisons['net_month_vs_last_month']) }}% {{ $d('vs_last_month') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Attention needed --}}
            <div class="dash-alert-grid" id="dash-alert-grid">
                @if(($operations['outstanding_due'] ?? 0) > 0)
                    <a href="{{ route('admin.invoices.index') }}" class="dash-alert danger">
                        <div class="dash-alert-icon text-danger"><i class="fas fa-file-invoice-dollar"></i></div>
                        <div>
                            <strong>{{ $d('patient_invoice_due') }}</strong>
                            <div class="small text-muted">৳ <span data-live="operations.outstanding_due" data-live-fmt="money">{{ $fmt($operations['outstanding_due']) }}</span> {{ $d('invoice_due_suffix') }}</div>
                        </div>
                    </a>
                @endif
                @if(($operations['refer_fee_due'] ?? 0) > 0)
                    <a href="{{ route('admin.reports.references.payment') }}" class="dash-alert warn">
                        <div class="dash-alert-icon text-warning"><i class="fas fa-user-tie"></i></div>
                        <div>
                            <strong>{{ $d('referrer_commission_due') }}</strong>
                            <div class="small text-muted">৳ <span data-live="operations.refer_fee_due" data-live-fmt="money">{{ $fmt($operations['refer_fee_due']) }}</span> {{ $d('payout_suffix') }}</div>
                        </div>
                    </a>
                @endif
                @if(($operations['pending_lab_tests'] ?? 0) > 0)
                    <a href="{{ route('admin.labs.index') }}" class="dash-alert info">
                        <div class="dash-alert-icon text-primary"><i class="fas fa-flask"></i></div>
                        <div>
                            <strong><span data-live="operations.pending_lab_tests">{{ $operations['pending_lab_tests'] }}</span> {{ $d('lab_tests_pending_label') }}</strong>
                            <div class="small text-muted"><span data-live="operations.completed_lab_today">{{ $operations['completed_lab_today'] }}</span> {{ $d('completed_today_label') }}</div>
                        </div>
                    </a>
                @endif
                @if(($pharmacy['low_stock'] ?? 0) + ($pharmacy['out_of_stock'] ?? 0) > 0)
                    <a href="{{ route('admin.reports.pharmacy-stock') }}" class="dash-alert warn">
                        <div class="dash-alert-icon text-warning"><i class="fas fa-pills"></i></div>
                        <div>
                            <strong>{{ $d('pharmacy_stock_alert') }}</strong>
                            <div class="small text-muted"><span data-live="pharmacy.out_of_stock">{{ $pharmacy['out_of_stock'] }}</span> {{ $d('stock_out') }} · <span data-live="pharmacy.low_stock">{{ $pharmacy['low_stock'] }}</span> {{ $d('stock_low') }}</div>
                        </div>
                    </a>
                @endif
            </div>

            {{-- Quick actions --}}
            <div class="dash-quick-grid">
                @if($userGuard->can('invoices.create'))
                    <a href="{{ route('admin.invoices.create') }}" class="dash-quick"><i class="fas fa-file-medical"></i> {{ $d('new_invoice') }}</a>
                @endif
                @if($userGuard->can('pharmacy_sales.create'))
                    <a href="{{ route('admin.pharmacy_sales.create') }}" class="dash-quick"><i class="fas fa-cash-register"></i> {{ $d('pharmacy_sale') }}</a>
                @endif
                @if($userGuard->can('admits.index'))
                    <a href="{{ route('admin.admits.index') }}" class="dash-quick"><i class="fas fa-procedures"></i> {{ t('menu.admits') }}</a>
                @endif
                @if($userGuard->can('reports.index'))
                    <a href="{{ route('admin.reports.collections') }}" class="dash-quick"><i class="fas fa-chart-bar"></i> {{ $d('collections') }}</a>
                @endif
                @if($userGuard->can('reports.index'))
                    <a href="{{ route('admin.reports.balance') }}" class="dash-quick"><i class="fas fa-balance-scale"></i> {{ t('common.balance') }}</a>
                @endif
                @if($userGuard->can('doctor_serials.index'))
                    <a href="{{ route('admin.doctor_serials.index') }}" class="dash-quick"><i class="fas fa-list-ol"></i> {{ $d('doctor_serial') }}</a>
                @endif
            </div>

            {{-- Operations snapshot --}}
            <div class="dash-ops-grid">
                <a href="{{ route('admin.invoices.index') }}" class="dash-op">
                    <span class="dash-op-icon blue"><i class="fas fa-file-invoice"></i></span>
                    <div class="dash-op-label">Today's Invoices</div>
                    <div class="dash-op-value" data-live="operations.today_invoices">{{ $operations['today_invoices'] }}</div>
                    <div class="dash-op-sub">৳ <span data-live="operations.today_invoice_amount" data-live-fmt="money">{{ $fmt($operations['today_invoice_amount']) }}</span> billed</div>
                </a>
                <a href="{{ route('admin.admits.index') }}" class="dash-op">
                    <span class="dash-op-icon green"><i class="fas fa-procedures"></i></span>
                    <div class="dash-op-label">Active Admits</div>
                    <div class="dash-op-value" data-live="operations.active_admits">{{ $operations['active_admits'] }}</div>
                    <div class="dash-op-sub">Currently admitted</div>
                </a>
                <div class="dash-op">
                    <span class="dash-op-icon amber"><i class="fas fa-flask"></i></span>
                    <div class="dash-op-label">Pending Lab Tests</div>
                    <div class="dash-op-value" data-live="operations.pending_lab_tests">{{ $operations['pending_lab_tests'] }}</div>
                    <div class="dash-op-sub"><span data-live="operations.completed_lab_today">{{ $operations['completed_lab_today'] }}</span> completed today</div>
                </div>
                <div class="dash-op">
                    <span class="dash-op-icon slate"><i class="fas fa-money-check-alt"></i></span>
                    <div class="dash-op-label">Payments Today</div>
                    <div class="dash-op-value" data-live="operations.payments_today">{{ $operations['today_invoice_payments'] + $operations['today_hospital_payments'] }}</div>
                    <div class="dash-op-sub">Inv <span data-live="operations.today_invoice_payments">{{ $operations['today_invoice_payments'] }}</span> · Hosp <span data-live="operations.today_hospital_payments">{{ $operations['today_hospital_payments'] }}</span></div>
                </div>
                <div class="dash-op">
                    <span class="dash-op-icon rose"><i class="fas fa-exclamation-circle"></i></span>
                    <div class="dash-op-label">Outstanding Due</div>
                    <div class="dash-op-value">৳ <span data-live="operations.outstanding_due" data-live-fmt="money">{{ $fmt($operations['outstanding_due']) }}</span></div>
                    <div class="dash-op-sub">Unpaid invoice balance</div>
                </div>
                <a href="{{ route('admin.reports.references.payment') }}" class="dash-op">
                    <span class="dash-op-icon amber"><i class="fas fa-hand-holding-usd"></i></span>
                    <div class="dash-op-label">Refer Fee Due</div>
                    <div class="dash-op-value">৳ <span data-live="operations.refer_fee_due" data-live-fmt="money">{{ $fmt($operations['refer_fee_due'] ?? 0) }}</span></div>
                    <div class="dash-op-sub">Commission not yet paid</div>
                </a>
                <a href="{{ route('admin.pharmacy_sales.index') }}" class="dash-op">
                    <span class="dash-op-icon green"><i class="fas fa-pills"></i></span>
                    <div class="dash-op-label">Pharmacy Today</div>
                    <div class="dash-op-value" data-live="pharmacy.sales_today">{{ $pharmacy['sales_today'] ?? 0 }}</div>
                    <div class="dash-op-sub">৳ <span data-live="pharmacy.collected_today" data-live-fmt="money">{{ $fmt($pharmacy['collected_today'] ?? 0) }}</span> collected</div>
                </a>
                <a href="{{ route('admin.doctor_serials.index') }}" class="dash-op">
                    <span class="dash-op-icon cyan"><i class="fas fa-user-md"></i></span>
                    <div class="dash-op-label">OPD Serials Today</div>
                    <div class="dash-op-value" data-live="opd.total_today">{{ $opd['total_today'] ?? 0 }}</div>
                    <div class="dash-op-sub"><span data-live="opd.pending">{{ $opd['pending'] ?? 0 }}</span> pending · <span data-live="opd.completed">{{ $opd['completed'] ?? 0 }}</span> done</div>
                </a>
                <div class="dash-op">
                    <span class="dash-op-icon cyan"><i class="fas fa-sms"></i></span>
                    <div class="dash-op-label">SMS Balance</div>
                    <div class="dash-op-value" data-live="operations.sms_balance">{{ $operations['sms_balance'] }}</div>
                    <div class="dash-op-sub">Remaining credits</div>
                </div>
            </div>

            {{-- Chart + Today breakdown --}}
            <div class="dash-split-layout mb-3">
                <div class="inv-panel mb-0">
                    <div class="inv-panel-head" style="cursor: default;">
                        <h6><i class="fas fa-chart-area me-2 text-primary"></i> Last 7 Days — Collection vs Cost</h6>
                    </div>
                    <div class="p-3">
                        <div id="dash-week-chart"></div>
                    </div>
                </div>

                <div>
                    <div class="dash-period dash-today-highlight mb-3">
                        <div class="dash-period-head">
                            <span class="dash-period-title"><i class="fas fa-sun me-1"></i> Today Breakdown</span>
                        </div>
                        <div class="dash-period-total">৳ <span data-live="today.collection.total" data-live-fmt="money">{{ $fmt($today['collection']['total']) }}</span></div>
                        <div class="dash-breakdown mb-3">
                            <span class="dash-pill inv">Invoice ৳<span data-live="today.collection.invoice" data-live-fmt="money">{{ $fmt($today['collection']['invoice']) }}</span></span>
                            <span class="dash-pill rec">Recept ৳<span data-live="today.collection.recept" data-live-fmt="money">{{ $fmt($today['collection']['recept']) }}</span></span>
                            <span class="dash-pill pharm">Pharmacy ৳<span data-live="today.collection.pharmacy" data-live-fmt="money">{{ $fmt($today['collection']['pharmacy'] ?? 0) }}</span></span>
                            <span class="dash-pill earn">Earn ৳<span data-live="today.collection.earn" data-live-fmt="money">{{ $fmt($today['collection']['earn']) }}</span></span>
                        </div>
                        <div class="dash-breakdown">
                            <span class="dash-pill cost">Cost ৳<span data-live="today.cost" data-live-fmt="money">{{ $fmt($today['cost']) }}</span></span>
                            <span class="dash-pill net">Net ৳<span data-live="today.net" data-live-fmt="money">{{ $fmt($today['net']) }}</span></span>
                        </div>
                    </div>
                    <div class="inv-panel mb-0">
                        <div class="inv-panel-head" style="cursor: default;">
                            <h6><i class="fas fa-chart-pie me-2 text-primary"></i> Today's Mix</h6>
                        </div>
                        <div class="p-2">
                            <div id="dash-today-split"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Top tests today --}}
            <div class="inv-panel mb-3" id="dash-top-tests-panel" @if(($topTestsToday ?? collect())->isEmpty()) style="display:none" @endif>
                <div class="inv-panel-head" style="cursor: default;">
                    <h6><i class="fas fa-vial me-2 text-primary"></i> Top Tests / Services Today</h6>
                    <small class="text-muted">By line count</small>
                </div>
                <div class="table-responsive">
                    <table class="inv-table table mb-0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Test / Product</th>
                            <th class="text-end">Lines</th>
                            <th class="text-end">Net Amount</th>
                        </tr>
                        </thead>
                        <tbody id="dash-top-tests-body">
                        @foreach($topTestsToday ?? [] as $row)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $row->product?->name ?? 'Unknown test' }}</td>
                                <td class="text-end fw-semibold">{{ $row->line_count }}</td>
                                <td class="text-end">৳ {{ $fmt($row->net_amount) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Period comparison --}}
            <div class="inv-panel mb-3">
                <div class="inv-panel-head" style="cursor: default;">
                    <h6><i class="fas fa-layer-group me-2 text-primary"></i> Collection & Cost by Period</h6>
                    <small class="text-muted">Based on actual payments received</small>
                </div>
                <div class="p-3">
                    <div class="dash-period-grid" id="dash-periods-grid">
                        @foreach([
                            ['label' => 'This Week', 'data' => $thisWeek, 'highlight' => false],
                            ['label' => 'This Month', 'data' => $thisMonth, 'highlight' => false],
                            ['label' => 'Last Week', 'data' => $lastWeek, 'highlight' => false],
                            ['label' => 'Last Month', 'data' => $lastMonth, 'highlight' => false],
                            ['label' => 'Last Year', 'data' => $lastYear, 'highlight' => false],
                        ] as $period)
                            <div class="dash-period {{ !empty($period['highlight']) ? 'dash-today-highlight' : '' }}">
                                <div class="dash-period-head">
                                    <span class="dash-period-title">{{ $period['label'] }}</span>
                                    <small class="text-muted">{{ $period['data']['from'] }} → {{ $period['data']['to'] }}</small>
                                </div>
                                <div class="dash-period-total {{ $period['data']['net'] < 0 ? 'negative' : '' }}">
                                    Net ৳{{ $fmt($period['data']['net']) }}
                                </div>
                                <div class="dash-breakdown">
                                    <span class="dash-pill inv">Inv ৳{{ $fmt($period['data']['collection']['invoice']) }}</span>
                                    <span class="dash-pill rec">Rec ৳{{ $fmt($period['data']['collection']['recept']) }}</span>
                                    <span class="dash-pill pharm">Pharm ৳{{ $fmt($period['data']['collection']['pharmacy'] ?? 0) }}</span>
                                    <span class="dash-pill earn">Earn ৳{{ $fmt($period['data']['collection']['earn']) }}</span>
                                    <span class="dash-pill cost">Cost ৳{{ $fmt($period['data']['cost']) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Recent invoices + Active admits --}}
            <div class="dash-layout">
                <div class="inv-panel">
                    <div class="inv-panel-head" style="cursor: default;">
                        <h6><i class="fas fa-file-invoice-dollar me-2 text-primary"></i> Recent Invoices</h6>
                        <a href="{{ route('admin.invoices.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="inv-table table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Patient</th>
                                    <th>Date</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-end">Paid</th>
                                    <th class="text-end">Due</th>
                                </tr>
                            </thead>
                            <tbody id="dash-recent-invoices-body">
                                @forelse($recentInvoices as $inv)
                                    @php
                                        $paid = (float) ($inv->paid_amount_sum_paid_amount ?? $inv->paid_amount_sum ?? 0);
                                        $due = max(0, (float) $inv->total_amount - $paid);
                                    @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.invoices.show', $inv->id) }}" class="fw-semibold text-primary">
                                                {{ $inv->invoice_number }}
                                            </a>
                                        </td>
                                        <td>{{ $inv->patient_name }}</td>
                                        <td>{{ $inv->creation_date ? \Carbon\Carbon::parse($inv->creation_date)->format('d M Y') : '—' }}</td>
                                        <td class="text-end">৳ {{ $fmt($inv->total_amount) }}</td>
                                        <td class="text-end">৳ {{ $fmt($paid) }}</td>
                                        <td class="text-end">
                                            @if($due > 0)
                                                <span class="dash-due-badge">৳ {{ $fmt($due) }}</span>
                                            @else
                                                <span class="dash-due-badge paid">Paid</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted py-4">No invoices yet</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="inv-panel">
                    <div class="inv-panel-head" style="cursor: default;">
                        <h6><i class="fas fa-bed me-2 text-primary"></i> Active Admits</h6>
                        <a href="{{ route('admin.admits.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="inv-table table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Admitted</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="dash-active-admits-body">
                                @forelse($activeAdmits as $admit)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ optional($admit->user)->name ?? '—' }}</div>
                                            @if($admit->drreefer)
                                                <small class="text-muted">Dr. {{ $admit->drreefer->name ?? '' }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $admit->created_at ? $admit->created_at->format('d M Y') : '—' }}</small>
                                        </td>
                                        <td class="text-end">
                                            @if($userGuard->can('admits.view'))
                                                <a href="{{ route('admin.admits.release.details', $admit->id) }}" class="btn btn-sm btn-outline-success">
                                                    Manage
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted py-4">No active admits</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            </div>{{-- /#dash-live-root --}}

        @else
            <div class="inv-panel">
                <div class="p-5 text-center">
                    <i class="fas fa-home fa-3x text-muted mb-3"></i>
                    <h3>{{ t('common.welcome') }}</h3>
                    <p class="text-muted mb-0">{{ $d('no_permission') }}</p>
                </div>
            </div>
        @endif

    </div>
@endsection

@push('scripts')
    @if ($userGuard && $userGuard->can('dashboards.view') && !empty($chart))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof ApexCharts === 'undefined') return;

                var fmtMoney = function (n) {
                    return Number(n || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                };

                var getPath = function (obj, path) {
                    return path.split('.').reduce(function (acc, key) {
                        return acc != null ? acc[key] : undefined;
                    }, obj);
                };

                var escapeHtml = function (str) {
                    return String(str == null ? '' : str)
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;');
                };

                var trendMeta = function (v) {
                    if (v === null || v === undefined) {
                        return { cls: 'flat', icon: 'fa-minus', text: '— vs previous' };
                    }
                    var cls = v > 0 ? 'up' : (v < 0 ? 'down' : 'flat');
                    var icon = v > 0 ? 'fa-arrow-up' : (v < 0 ? 'fa-arrow-down' : 'fa-minus');
                    return { cls: cls, icon: icon, text: Math.abs(v) + '%' };
                };

                var prevHandling = {{ (int) ($patients['handling_now'] ?? 0) }};
                var canManageAdmits = {{ $userGuard->can('admits.index') ? 'true' : 'false' }};

                window.dashWeekChart = new ApexCharts(document.querySelector('#dash-week-chart'), {
                    chart: { type: 'area', height: 320, toolbar: { show: false }, fontFamily: 'inherit' },
                    series: [
                        { name: 'Collection', data: @json($chart['collection']) },
                        { name: 'Cost', data: @json($chart['cost']) },
                        { name: 'Net', data: @json($chart['net']) },
                    ],
                    colors: ['#059669', '#dc2626', '#2563eb'],
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 2 },
                    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05, stops: [0, 90, 100] } },
                    xaxis: { categories: @json($chart['labels']), labels: { style: { colors: '#64748b', fontSize: '12px' } } },
                    yaxis: { labels: { formatter: function (v) { return '৳' + v.toLocaleString(); }, style: { colors: '#64748b' } } },
                    tooltip: { y: { formatter: function (v) { return '৳ ' + v.toLocaleString(undefined, { minimumFractionDigits: 2 }); } } },
                    legend: { position: 'top', horizontalAlign: 'right' },
                    grid: { borderColor: '#e2e8f0', strokeDashArray: 4 },
                });
                window.dashWeekChart.render();

                @if(!empty($chartTodaySplit))
                window.dashSplitChart = new ApexCharts(document.querySelector('#dash-today-split'), {
                    chart: { type: 'donut', height: 280, fontFamily: 'inherit' },
                    series: @json($chartTodaySplit['values']),
                    labels: @json($chartTodaySplit['labels']),
                    colors: ['#2563eb', '#059669', '#7c3aed', '#d97706'],
                    legend: { position: 'bottom', fontSize: '12px' },
                    dataLabels: { enabled: true, formatter: function (v) { return v.toFixed(1) + '%'; } },
                    tooltip: { y: { formatter: function (v) { return '৳ ' + v.toLocaleString(undefined, { minimumFractionDigits: 2 }); } } },
                    plotOptions: { pie: { donut: { size: '62%' } } },
                    noData: { text: 'No collection yet today' },
                });
                window.dashSplitChart.render();
                @endif

                @if(!empty($patientInsights['chart_today_segments']))
                window.dashPatientChart = new ApexCharts(document.querySelector('#dash-patient-segment-chart'), {
                    chart: { type: 'donut', height: 260, fontFamily: 'inherit' },
                    series: @json($patientInsights['chart_today_segments']['values'] ?? []),
                    labels: @json($patientInsights['chart_today_segments']['labels'] ?? []),
                    colors: ['#d97706', '#059669', '#2563eb', '#0891b2', '#64748b', '#ea580c'],
                    legend: { position: 'bottom', fontSize: '11px' },
                    dataLabels: { enabled: true },
                    plotOptions: { pie: { donut: { size: '58%' } } },
                    noData: { text: 'No patients today yet' },
                });
                window.dashPatientChart.render();
                @endif

                var renderPredictions = function (predictions) {
                    var grid = document.getElementById('dash-predict-grid');
                    if (!grid) return;
                    if (!predictions || !predictions.length) {
                        grid.innerHTML = '<div class="dash-predict info"><div class="dash-predict-icon"><i class="fas fa-info-circle"></i></div><div><strong>Building patient patterns</strong><div class="small text-muted">More visits = smarter special/regular detection</div></div></div>';
                        return;
                    }
                    grid.innerHTML = predictions.map(function (p) {
                        return '<div class="dash-predict ' + escapeHtml(p.tone || 'info') + '">' +
                            '<div class="dash-predict-icon"><i class="fas ' + escapeHtml(p.icon || 'fa-lightbulb') + '"></i></div>' +
                            '<div><strong>' + escapeHtml(p.title) + '</strong>' +
                            '<div class="small text-muted">' + escapeHtml(p.subtitle) + '</div></div></div>';
                    }).join('');
                };

                var renderTodayPatients = function (rows) {
                    var body = document.getElementById('dash-today-patients-body');
                    if (!body) return;
                    if (!rows || !rows.length) {
                        body.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">No patient activity yet today</td></tr>';
                        return;
                    }
                    body.innerHTML = rows.map(function (row) {
                        return '<tr><td><div class="fw-semibold">' + escapeHtml(row.name) + '</div><small class="text-muted">' + escapeHtml(row.phone) + '</small></td>' +
                            '<td><span class="dash-patient-badge ' + escapeHtml(row.segment) + '">' + escapeHtml(row.segment_label) + '</span></td>' +
                            '<td class="text-end">' + row.visit_count + '</td>' +
                            '<td class="text-end">৳ ' + fmtMoney(row.total_spent) + '</td>' +
                            '<td><small class="text-muted">' + escapeHtml(row.prediction) + '</small></td></tr>';
                    }).join('');
                };

                var renderPatientInsights = function (insights) {
                    if (!insights) return;
                    renderPredictions(insights.predictions);
                    renderTodayPatients(insights.today_patients);
                    if (window.dashPatientChart && insights.chart_today_segments) {
                        window.dashPatientChart.updateSeries(insights.chart_today_segments.values || []);
                    }
                };

                var renderTrend = function (elId, value, suffix) {
                    var el = document.getElementById(elId);
                    if (!el) return;
                    var meta = trendMeta(value);
                    el.className = 'dash-trend ' + meta.cls;
                    el.innerHTML = '<i class="fas ' + meta.icon + '"></i> ' + (value === null || value === undefined ? meta.text : meta.text + ' ' + suffix);
                };

                var renderAlerts = function (alerts) {
                    var grid = document.getElementById('dash-alert-grid');
                    if (!grid) return;
                    if (!alerts || !alerts.length) {
                        grid.innerHTML = '';
                        grid.style.display = 'none';
                        return;
                    }
                    grid.style.display = '';
                    grid.innerHTML = alerts.map(function (a) {
                        return '<a href="' + escapeHtml(a.url) + '" class="dash-alert ' + escapeHtml(a.type) + '">' +
                            '<div class="dash-alert-icon ' + escapeHtml(a.icon_class) + '"><i class="fas ' + escapeHtml(a.icon) + '"></i></div>' +
                            '<div><strong>' + escapeHtml(a.title) + '</strong>' +
                            '<div class="small text-muted">' + escapeHtml(a.subtitle) + '</div></div></a>';
                    }).join('');
                };

                var renderPeriods = function (periods) {
                    var grid = document.getElementById('dash-periods-grid');
                    if (!grid || !periods) return;
                    grid.innerHTML = periods.map(function (p) {
                        var netClass = p.net < 0 ? ' negative' : '';
                        return '<div class="dash-period">' +
                            '<div class="dash-period-head">' +
                            '<span class="dash-period-title">' + escapeHtml(p.label) + '</span>' +
                            '<small class="text-muted">' + escapeHtml(p.from) + ' → ' + escapeHtml(p.to) + '</small>' +
                            '</div>' +
                            '<div class="dash-period-total' + netClass + '">Net ৳' + fmtMoney(p.net) + '</div>' +
                            '<div class="dash-breakdown">' +
                            '<span class="dash-pill inv">Inv ৳' + fmtMoney(p.collection.invoice) + '</span>' +
                            '<span class="dash-pill rec">Rec ৳' + fmtMoney(p.collection.recept) + '</span>' +
                            '<span class="dash-pill pharm">Pharm ৳' + fmtMoney(p.collection.pharmacy || 0) + '</span>' +
                            '<span class="dash-pill earn">Earn ৳' + fmtMoney(p.collection.earn) + '</span>' +
                            '<span class="dash-pill cost">Cost ৳' + fmtMoney(p.cost) + '</span>' +
                            '</div></div>';
                    }).join('');
                };

                var renderTopTests = function (rows) {
                    var panel = document.getElementById('dash-top-tests-panel');
                    var body = document.getElementById('dash-top-tests-body');
                    if (!body || !panel) return;
                    if (!rows || !rows.length) {
                        panel.style.display = 'none';
                        return;
                    }
                    panel.style.display = '';
                    body.innerHTML = rows.map(function (row, i) {
                        return '<tr><td>' + (i + 1) + '</td><td>' + escapeHtml(row.name) + '</td><td class="text-end fw-semibold">' + row.line_count + '</td><td class="text-end">৳ ' + fmtMoney(row.net_amount) + '</td></tr>';
                    }).join('');
                };

                var renderRecentInvoices = function (rows) {
                    var body = document.getElementById('dash-recent-invoices-body');
                    if (!body) return;
                    if (!rows || !rows.length) {
                        body.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No invoices yet</td></tr>';
                        return;
                    }
                    body.innerHTML = rows.map(function (inv) {
                        var dueCell = inv.due > 0
                            ? '<span class="dash-due-badge">৳ ' + fmtMoney(inv.due) + '</span>'
                            : '<span class="dash-due-badge paid">Paid</span>';
                        return '<tr><td><a href="' + escapeHtml(inv.show_url) + '" class="fw-semibold text-primary">' + escapeHtml(inv.invoice_number) + '</a></td><td>' + escapeHtml(inv.patient_name) + '</td><td>' + escapeHtml(inv.creation_date) + '</td><td class="text-end">৳ ' + fmtMoney(inv.total_amount) + '</td><td class="text-end">৳ ' + fmtMoney(inv.paid) + '</td><td class="text-end">' + dueCell + '</td></tr>';
                    }).join('');
                };

                var renderActiveAdmits = function (rows) {
                    var body = document.getElementById('dash-active-admits-body');
                    if (!body) return;
                    if (!rows || !rows.length) {
                        body.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-4">No active admits</td></tr>';
                        return;
                    }
                    body.innerHTML = rows.map(function (admit) {
                        var doctor = admit.doctor_name ? '<small class="text-muted">Dr. ' + escapeHtml(admit.doctor_name) + '</small>' : '';
                        var manage = canManageAdmits ? '<a href="' + escapeHtml(admit.manage_url) + '" class="btn btn-sm btn-outline-success">Manage</a>' : '';
                        return '<tr><td><div class="fw-semibold">' + escapeHtml(admit.patient_name) + '</div>' + doctor + '</td><td><small>' + escapeHtml(admit.admitted_at) + '</small></td><td class="text-end">' + manage + '</td></tr>';
                    }).join('');
                };

                var applyLiveData = function (data) {
                    var root = document.getElementById('dash-live-root');
                    if (root) {
                        root.classList.add('dash-sync-flash');
                        setTimeout(function () { root.classList.remove('dash-sync-flash'); }, 350);
                    }

                    document.querySelectorAll('[data-live]').forEach(function (el) {
                        var path = el.getAttribute('data-live');
                        var val = getPath(data, path);
                        if (val === undefined || val === null) return;

                        var display = el.getAttribute('data-live-fmt') === 'money' ? fmtMoney(val) : String(val);
                        if (el.textContent.trim() !== display) {
                            el.textContent = display;
                            el.classList.add('live-changed');
                            setTimeout(function () { el.classList.remove('live-changed'); }, 800);
                        }
                    });

                    var netKpi = document.getElementById('dash-kpi-net-value');
                    if (netKpi && data.today) {
                        netKpi.classList.toggle('text-danger', Number(data.today.net) < 0);
                    }

                    if (data.comparisons) {
                        renderTrend('dash-trend-collection', data.comparisons.collection_vs_yesterday, 'vs yesterday');
                        renderTrend('dash-trend-net', data.comparisons.net_vs_yesterday, 'vs yesterday');
                        renderTrend('dash-trend-week', data.comparisons.net_week_vs_last_week, 'vs last week');
                        renderTrend('dash-trend-month', data.comparisons.net_month_vs_last_month, 'vs last month');
                    }

                    var handlingEl = document.getElementById('dash-live-handling');
                    if (handlingEl && data.patients) {
                        var next = Number(data.patients.handling_now || 0);
                        var display = String(next);
                        if (handlingEl.textContent.trim() !== display) {
                            handlingEl.textContent = display;
                        }
                        if (next !== prevHandling) {
                            handlingEl.classList.remove('flash-up', 'flash-down');
                            handlingEl.classList.add(next > prevHandling ? 'flash-up' : 'flash-down');
                            prevHandling = next;
                            setTimeout(function () {
                                handlingEl.classList.remove('flash-up', 'flash-down');
                            }, 600);
                        }
                    }

                    if (data.updated_at) {
                        var updated = document.getElementById('dash-live-updated');
                        var clock = document.getElementById('dash-live-clock');
                        if (updated) updated.textContent = data.updated_at;
                        if (clock) clock.textContent = data.updated_at;
                    }

                    if (window.dashWeekChart && data.chart) {
                        window.dashWeekChart.updateSeries([
                            { name: 'Collection', data: data.chart.collection },
                            { name: 'Cost', data: data.chart.cost },
                            { name: 'Net', data: data.chart.net },
                        ]);
                    }

                    if (window.dashSplitChart && data.chartTodaySplit) {
                        window.dashSplitChart.updateSeries(data.chartTodaySplit.values);
                    }

                    renderAlerts(data.alerts);
                    renderPeriods(data.periods);
                    renderTopTests(data.topTestsToday);
                    renderRecentInvoices(data.recentInvoices);
                    renderActiveAdmits(data.activeAdmits);
                    renderPatientInsights(data.patientInsights);
                };

                var liveUrl = @json(route('admin.dashboard.live'));
                var pollMs = 3000;
                var pollTimer = null;
                var fetching = false;

                var fetchLive = function () {
                    if (fetching) return;
                    fetching = true;
                    fetch(liveUrl, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin',
                    })
                        .then(function (r) { return r.ok ? r.json() : Promise.reject(r); })
                        .then(applyLiveData)
                        .catch(function () { /* silent retry on next tick */ })
                        .finally(function () { fetching = false; });
                };

                var startPolling = function () {
                    if (pollTimer) clearInterval(pollTimer);
                    pollTimer = setInterval(fetchLive, pollMs);
                };

                var stopPolling = function () {
                    if (pollTimer) clearInterval(pollTimer);
                    pollTimer = null;
                };

                document.addEventListener('visibilitychange', function () {
                    if (document.hidden) {
                        stopPolling();
                    } else {
                        fetchLive();
                        startPolling();
                    }
                });

                fetchLive();
                startPolling();

                @if($userGuard && $userGuard->can('ai.analytics'))
                var aiInsightsUrl = @json(route('admin.dashboard.ai-insights'));
                var aiContent = document.getElementById('dash-ai-insights-content');
                var aiMeta = document.getElementById('dash-ai-insights-meta');
                var aiLoading = document.getElementById('dash-ai-insights-loading');
                var aiRefresh = document.getElementById('dash-ai-refresh');

                var loadAiInsights = function (refresh) {
                    if (!aiContent) return;
                    if (aiLoading) aiLoading.classList.remove('d-none');
                    var url = aiInsightsUrl + (refresh ? '?refresh=1' : '');
                    fetch(url, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin',
                    })
                        .then(function (r) { return r.ok ? r.json() : Promise.reject(r); })
                        .then(function (data) {
                            if (!aiContent) return;
                            var html = '';
                            if (data.health_score != null) {
                                html += '<div class="mb-2"><span class="badge bg-dark">Health ' + data.health_score + '/100</span></div>';
                            }
                            if (data.priority_actions && data.priority_actions.length) {
                                html += '<ul class="small mb-2 ps-3">';
                                data.priority_actions.slice(0, 3).forEach(function (a) {
                                    html += '<li><strong>' + a.title + '</strong> — ' + a.detail + '</li>';
                                });
                                html += '</ul>';
                            }
                            if (data.insights) {
                                html += '<div style="white-space:pre-wrap">' + data.insights + '</div>';
                            }
                            aiContent.innerHTML = html || '—';
                            aiContent.classList.remove('dash-ai-empty');
                            if (aiMeta && data.generated_at) {
                                aiMeta.textContent = data.generated_at;
                            }
                        })
                        .catch(function () {
                            if (aiContent) aiContent.textContent = @json(t('ai.request_failed'));
                        })
                        .finally(function () {
                            if (aiLoading) aiLoading.classList.add('d-none');
                        });
                };

                loadAiInsights(false);
                if (aiRefresh) {
                    aiRefresh.addEventListener('click', function () { loadAiInsights(true); });
                }
                @endif
            });
        </script>
    @endif
@endpush
