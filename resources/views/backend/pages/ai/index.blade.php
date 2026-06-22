@extends('backend.layouts.master')
@section('title')
    {{ t('menu.ai_module') }}
@endsection

@push('styles')
    @include('backend.layouts.partials.invoice-styles')
    <style>
        .ai-hub-hero {
            background: linear-gradient(135deg, #0f172a 0%, #134e4a 50%, #0f766e 100%);
            border-radius: 18px;
            padding: 32px;
            color: #fff;
            margin-bottom: 24px;
        }

        .ai-command-grid {
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }

        @media (max-width: 991px) {
            .ai-command-grid { grid-template-columns: 1fr; }
        }

        .ai-score-ring {
            background: #fff;
            border: 1px solid var(--inv-border);
            border-radius: 16px;
            padding: 24px;
            text-align: center;
        }

        .ai-score-value {
            font-size: 2.8rem;
            font-weight: 800;
            line-height: 1;
        }

        .ai-score-value.excellent { color: #059669; }
        .ai-score-value.good { color: #0d9488; }
        .ai-score-value.fair { color: #d97706; }
        .ai-score-value.critical { color: #dc2626; }

        .ai-kpi-row {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .ai-kpi-card {
            background: #f8fafc;
            border: 1px solid var(--inv-border);
            border-radius: 12px;
            padding: 14px;
        }

        .ai-kpi-card .label { font-size: 0.75rem; color: #64748b; }
        .ai-kpi-card .value { font-size: 1.05rem; font-weight: 700; color: #0f172a; }

        .ai-command-panel {
            background: #fff;
            border: 1px solid var(--inv-border);
            border-radius: 16px;
            padding: 22px;
        }

        .ai-action-item {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .ai-action-item:last-child { border-bottom: 0; }

        .ai-severity {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 3px 8px;
            border-radius: 6px;
            flex-shrink: 0;
        }

        .ai-severity.high { background: #fef2f2; color: #dc2626; }
        .ai-severity.medium { background: #fffbeb; color: #d97706; }
        .ai-severity.low { background: #f0fdf4; color: #059669; }

        .ai-narrative {
            white-space: pre-wrap;
            line-height: 1.65;
            color: #334155;
            font-size: 0.92rem;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #f1f5f9;
        }

        .ai-forecast-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f8fafc;
            border: 1px solid var(--inv-border);
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 0.8rem;
            margin: 4px 6px 4px 0;
        }

        .ai-hub-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
        }

        .ai-hub-card {
            background: #fff;
            border: 1px solid var(--inv-border);
            border-radius: 16px;
            padding: 22px;
            height: 100%;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .ai-hub-card-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .ai-hub-card h3 { font-size: 1.05rem; font-weight: 700; margin: 0; }
        .ai-hub-card p { font-size: 0.88rem; color: #64748b; margin: 0; line-height: 1.55; flex: 1; }

        .ai-schema-note {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 0.88rem;
            margin-bottom: 20px;
        }
    </style>
@endpush

@section('admin-content')
<div class="inv-page container-fluid py-3">
    <div class="ai-hub-hero">
        <h1 class="h3 fw-bold mb-2">{{ t('menu.ai_module') }}</h1>
        <p class="mb-0 opacity-90">{{ t('ai.hub_advanced_subtitle') }}</p>
    </div>

    @if(!$schemaReady && auth('admin')->user()?->hasRole('Super Admin'))
        <div class="ai-schema-note">
            <i class="fas fa-database text-warning me-1"></i>
            {{ t('ai.schema_pending') }}
            <a href="{{ route('admin.home') }}" class="fw-semibold">{{ t('ai.go_dashboard_updates') }}</a>
        </div>
    @endif

    @if($canAnalytics)
    <div class="ai-command-grid" id="insights">
        <div class="ai-score-ring">
            <div class="small text-muted mb-2">{{ t('ai.health_score') }}</div>
            <div class="ai-score-value good" id="ai-hub-score">—</div>
            <div class="small fw-semibold mt-2 text-muted" id="ai-hub-score-label">—</div>
            <button type="button" class="btn btn-sm btn-outline-secondary mt-3 w-100" id="ai-hub-refresh">
                <i class="fas fa-sync-alt"></i> {{ t('dashboard.ai_refresh') }}
            </button>
        </div>

        <div class="ai-command-panel">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h2 class="h6 fw-bold mb-0"><i class="fas fa-bolt text-warning me-1"></i> {{ t('ai.priority_actions') }}</h2>
                <div id="ai-hub-insights-loading" class="text-muted small d-none"><i class="fas fa-spinner fa-spin"></i></div>
            </div>
            <div id="ai-hub-kpis" class="ai-kpi-row"></div>
            <div id="ai-hub-actions"></div>
            <div id="ai-hub-forecasts" class="mt-2"></div>
            <div id="ai-hub-insights" class="ai-narrative text-muted">—</div>
            <div class="small text-muted mt-2 text-end" id="ai-hub-insights-meta"></div>
        </div>
    </div>
    @endif

    <div class="ai-hub-grid">
        @if($canReports)
        <div class="ai-hub-card">
            <div class="ai-hub-card-icon" style="background:#eff6ff;color:#2563eb;"><i class="fas fa-file-alt"></i></div>
            <h3>{{ t('menu.ai_report_summary') }}</h3>
            <p>{{ t('ai.card_reports') }}</p>
            <a href="{{ route('admin.invoices.index') }}" class="btn btn-sm btn-dark">{{ t('ai.open_invoices') }}</a>
        </div>
        @endif

        @if($canHealth)
        <div class="ai-hub-card">
            <div class="ai-hub-card-icon" style="background:#ecfdf5;color:#0d9488;"><i class="fas fa-notes-medical"></i></div>
            <h3>{{ t('menu.ai_health') }}</h3>
            <p>{{ t('ai.card_health') }}</p>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-dark">{{ t('ai.open_patients') }}</a>
        </div>
        @endif

        @if($canChat)
        <div class="ai-hub-card" id="assistant">
            <div class="ai-hub-card-icon" style="background:#f5f3ff;color:#7c3aed;"><i class="fas fa-comment-dots"></i></div>
            <h3>{{ t('menu.ai_assistant') }}</h3>
            <p>{{ t('ai.card_chat') }}</p>
            <button type="button" class="btn btn-sm btn-dark" onclick="document.getElementById('ai-chat-fab')?.click()">
                {{ t('ai.open_assistant') }}
            </button>
        </div>
        @endif

        <div class="ai-hub-card">
            <div class="ai-hub-card-icon" style="background:#f8fafc;color:#475569;"><i class="fas fa-book"></i></div>
            <h3>{{ t('menu.ai_docs') }}</h3>
            <p>{{ t('ai.card_docs') }}</p>
            <a href="{{ $docsUrl }}" class="btn btn-sm btn-outline-secondary">{{ t('ai.read_docs') }}</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($canAnalytics)
@php
    $aiActionRoutes = [
        'admin.invoices.index' => route('admin.invoices.index'),
        'admin.labs.index' => route('admin.labs.index'),
        'admin.pharmacy_purchases.create' => route('admin.pharmacy_purchases.create'),
        'admin.pharmacy_products.index' => route('admin.pharmacy_products.index'),
        'admin.reports.references.payment' => route('admin.reports.references.payment'),
        'admin.users.index' => route('admin.users.index'),
        'admin.doctor_serials.index' => route('admin.doctor_serials.index'),
        'admin.invoices.create' => route('admin.invoices.create'),
    ];
@endphp
<script>
document.addEventListener('DOMContentLoaded', function () {
    var url = @json(route('admin.dashboard.ai-insights'));
    var routes = @json($aiActionRoutes);
    var labels = {
        excellent: @json(t('ai.score_excellent')),
        good: @json(t('ai.score_good')),
        fair: @json(t('ai.score_fair')),
        critical: @json(t('ai.score_critical')),
        view: @json(t('ai.view_action')),
        noActions: @json(t('ai.no_abnormalities')),
        failed: @json(t('ai.request_failed')),
    };

    var scoreEl = document.getElementById('ai-hub-score');
    var scoreLabel = document.getElementById('ai-hub-score-label');
    var kpisEl = document.getElementById('ai-hub-kpis');
    var actionsEl = document.getElementById('ai-hub-actions');
    var forecastsEl = document.getElementById('ai-hub-forecasts');
    var content = document.getElementById('ai-hub-insights');
    var meta = document.getElementById('ai-hub-insights-meta');
    var loading = document.getElementById('ai-hub-insights-loading');
    var refresh = document.getElementById('ai-hub-refresh');

    var render = function (data) {
        if (scoreEl && data.health_score != null) {
            scoreEl.textContent = data.health_score;
            var lbl = data.health_label || 'good';
            scoreEl.className = 'ai-score-value ' + lbl;
            scoreLabel.textContent = labels[lbl] || lbl;
        }

        if (kpisEl && data.kpis) {
            kpisEl.innerHTML = data.kpis.map(function (k) {
                return '<div class="ai-kpi-card"><div class="label">' + k.label + '</div><div class="value">' + k.value + '</div></div>';
            }).join('');
        }

        if (actionsEl) {
            var actions = data.priority_actions || [];
            if (!actions.length) {
                actionsEl.innerHTML = '<div class="text-muted small py-2">' + labels.noActions + '</div>';
            } else {
                actionsEl.innerHTML = actions.map(function (a) {
                    var href = routes[a.route] || '#';
                    return '<div class="ai-action-item">' +
                        '<span class="ai-severity ' + a.severity + '">' + a.severity + '</span>' +
                        '<div class="flex-grow-1"><div class="fw-semibold small">' + a.title + '</div>' +
                        '<div class="text-muted small">' + a.detail + '</div>' +
                        (href !== '#' ? '<a href="' + href + '" class="small fw-semibold">' + labels.view + ' →</a>' : '') +
                        '</div></div>';
                }).join('');
            }
        }

        if (forecastsEl && data.forecasts && data.forecasts.length) {
            forecastsEl.innerHTML = data.forecasts.map(function (f) {
                var icon = f.trend === 'up' ? 'fa-arrow-up text-success' : (f.trend === 'down' ? 'fa-arrow-down text-danger' : 'fa-minus text-muted');
                return '<span class="ai-forecast-chip"><i class="fas ' + icon + '"></i> ' + f.label + ': <strong>' + f.value + '</strong></span>';
            }).join('');
        }

        if (content && data.insights) {
            content.textContent = data.insights;
            content.classList.remove('text-muted');
        }
        if (meta && data.generated_at) meta.textContent = data.generated_at;
    };

    var load = function (force) {
        if (loading) loading.classList.remove('d-none');
        fetch(url + (force ? '?refresh=1' : ''), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        })
            .then(function (r) { return r.ok ? r.json() : Promise.reject(); })
            .then(render)
            .catch(function () {
                if (content) content.textContent = labels.failed;
            })
            .finally(function () {
                if (loading) loading.classList.add('d-none');
            });
    };

    load(false);
    refresh?.addEventListener('click', function () { load(true); });
});
</script>
@endif
@endpush
