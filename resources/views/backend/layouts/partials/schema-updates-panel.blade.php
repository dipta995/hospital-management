@php
    $schemaModules = $schemaModules ?? [];
    $pendingCount = $pendingCount ?? collect($schemaModules)->where('installed', false)->count();
    $compact = !empty($compact);
@endphp

@once
    @push('styles')
        <style>
            .schema-updates-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
                gap: 14px;
            }
            .schema-update-card {
                background: #fff;
                border: 1px solid #e2e8f0;
                border-radius: 14px;
                padding: 16px 18px;
                box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
            }
            .schema-update-card.is-ready {
                border-color: #bbf7d0;
                background: linear-gradient(180deg, #fff, #f0fdf4);
            }
            .schema-update-desc {
                font-size: 0.82rem;
                color: #64748b;
                margin-bottom: 10px;
                line-height: 1.45;
            }
            .schema-update-status {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
                list-style: none;
                padding: 0;
                margin: 0 0 12px;
            }
            .schema-update-status li {
                font-size: 0.72rem;
                font-weight: 700;
                padding: 4px 9px;
                border-radius: 999px;
                background: #f1f5f9;
                color: #64748b;
            }
            .schema-update-status li.ok {
                background: #dcfce7;
                color: #166534;
            }
            .schema-updates-toolbar {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                margin-bottom: 16px;
            }
        </style>
    @endpush
@endonce

@if(!$compact)
    <div class="schema-updates-toolbar">
        <div>
            <p class="mb-1 text-muted small">{{ t('dashboard.schema_updates_help') }}</p>
            @if($pendingCount > 0)
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                    {{ t('dashboard.schema_pending_count', ['count' => $pendingCount]) }}
                </span>
            @else
                <span class="badge bg-success-subtle text-success border border-success-subtle">
                    {{ t('dashboard.schema_all_ready') }}
                </span>
            @endif
        </div>
        @if($pendingCount > 0)
            <form method="post" action="{{ route('admin.system.install-all-schema') }}"
                  onsubmit="return confirm(@json(t('dashboard.schema_install_all_confirm')))">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-bolt me-1"></i> {{ t('dashboard.install_all_schema') }}
                </button>
            </form>
        @endif
    </div>
@endif

<div class="schema-updates-grid">
    @foreach($schemaModules as $module)
        <div class="schema-update-card {{ !empty($module['installed']) ? 'is-ready' : '' }}">
            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                <div>
                    <strong class="d-block">{{ $module['label'] }}</strong>
                    @if(!$compact && !empty($module['description']))
                        <div class="schema-update-desc">{{ $module['description'] }}</div>
                    @endif
                </div>
                @if(!empty($module['installed']))
                    <span class="badge bg-success-subtle text-success border border-success-subtle">{{ t('dashboard.ready') }}</span>
                @else
                    <span class="badge bg-light text-secondary border">{{ t('dashboard.pending') }}</span>
                @endif
            </div>
            <ul class="schema-update-status">
                @foreach($module['status_labels'] ?? [] as $item)
                    <li class="{{ !empty($item['ok']) ? 'ok' : '' }}">{{ $item['label'] }}</li>
                @endforeach
            </ul>
            @if(empty($module['installed']))
                <form method="post" action="{{ route('admin.system.install-schema', $module['key']) }}"
                      onsubmit="return confirm(@json(t('dashboard.schema_install_confirm')))">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-dark">
                        <i class="fas fa-arrow-up me-1"></i> {{ t('dashboard.install_schema') }}
                    </button>
                </form>
            @endif
        </div>
    @endforeach
</div>
