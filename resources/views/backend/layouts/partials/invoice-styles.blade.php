<style>
    .inv-page {
        --inv-primary: #0c4a6e;
        --inv-primary-light: #0369a1;
        --inv-accent: #06b6d4;
        --inv-accent-soft: #ecfeff;
        --inv-success: #059669;
        --inv-warning: #d97706;
        --inv-danger: #dc2626;
        --inv-surface: #ffffff;
        --inv-muted: #64748b;
        --inv-border: #e2e8f0;
        --inv-shadow: 0 12px 40px rgba(12, 74, 110, 0.08);
        --inv-radius: 16px;
    }

    /* ── Hero ── */
    .inv-hero {
        position: relative;
        overflow: hidden;
        border-radius: var(--inv-radius);
        padding: 28px 32px;
        margin-bottom: 22px;
        background: linear-gradient(135deg, #0c4a6e 0%, #0369a1 45%, #0891b2 100%);
        color: #fff;
        box-shadow: var(--inv-shadow);
    }

    .inv-hero::before {
        content: '';
        position: absolute;
        top: -60%;
        right: -10%;
        width: 420px;
        height: 420px;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
        pointer-events: none;
    }

    .inv-hero::after {
        content: '';
        position: absolute;
        bottom: -40%;
        left: 20%;
        width: 280px;
        height: 280px;
        border-radius: 50%;
        background: rgba(6,182,212,0.12);
        pointer-events: none;
    }

    .inv-hero-inner {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }

    .inv-hero-left {
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .inv-hero-icon {
        width: 58px;
        height: 58px;
        border-radius: 14px;
        background: rgba(255,255,255,0.14);
        border: 1px solid rgba(255,255,255,0.22);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        backdrop-filter: blur(8px);
    }

    .inv-hero-title {
        margin: 0;
        font-size: 1.65rem;
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .inv-hero-sub {
        margin: 4px 0 0;
        opacity: 0.88;
        font-size: 0.92rem;
    }

    .inv-hero-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .inv-btn-glass {
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.28);
        color: #fff;
        border-radius: 10px;
        padding: 0.6rem 1.15rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        backdrop-filter: blur(6px);
    }

    .inv-btn-glass:hover { background: #fff; color: var(--inv-primary); }

    .inv-btn-white {
        background: #fff;
        color: var(--inv-primary);
        border: none;
        border-radius: 10px;
        padding: 0.6rem 1.25rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        transition: transform 0.15s ease;
    }

    .inv-btn-white:hover { color: var(--inv-primary-light); transform: translateY(-2px); }

    /* ── KPI Cards ── */
    .inv-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 14px;
        margin-bottom: 20px;
    }

    .inv-kpi {
        background: var(--inv-surface);
        border: 1px solid var(--inv-border);
        border-radius: 14px;
        padding: 18px 20px;
        display: flex;
        align-items: flex-start;
        gap: 14px;
        box-shadow: 0 4px 16px rgba(15,23,42,0.04);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .inv-kpi:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 28px rgba(15,23,42,0.08);
    }

    .inv-kpi-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .inv-kpi-icon.own { background: #fef2f2; color: #dc2626; }
    .inv-kpi-icon.other { background: #fffbeb; color: #d97706; }
    .inv-kpi-icon.collection { background: #eff6ff; color: #2563eb; }
    .inv-kpi-icon.discount { background: #ecfdf5; color: #059669; }
    .inv-kpi-icon.due { background: #fdf2f8; color: #db2777; }

    .inv-kpi-label {
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--inv-muted);
        margin-bottom: 2px;
    }

    .inv-kpi-value {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }

    /* ── Filter Panel ── */
    .inv-panel {
        background: var(--inv-surface);
        border: 1px solid var(--inv-border);
        border-radius: var(--inv-radius);
        box-shadow: 0 4px 20px rgba(15,23,42,0.04);
        margin-bottom: 20px;
        overflow: hidden;
    }

    .inv-panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 22px;
        background: #f8fafc;
        border-bottom: 1px solid var(--inv-border);
        cursor: pointer;
        user-select: none;
    }

    .inv-panel-head h6 {
        margin: 0;
        font-weight: 700;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .inv-panel-head h6 i { color: var(--inv-accent); }

    .inv-panel-body {
        padding: 20px 22px;
    }

    .inv-panel-body .form-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 4px;
    }

    .inv-panel-body .form-control,
    .inv-panel-body .form-select {
        border-radius: 10px;
        border-color: #cbd5e1;
        min-height: 40px;
        font-size: 0.9rem;
    }

    .inv-panel-body .form-control:focus,
    .inv-panel-body .form-select:focus {
        border-color: var(--inv-accent);
        box-shadow: 0 0 0 3px rgba(6,182,212,0.15);
    }

    .inv-filter-actions {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        padding-top: 8px;
        border-top: 1px solid var(--inv-border);
        margin-top: 12px;
    }

    .inv-btn-filter {
        background: linear-gradient(135deg, var(--inv-primary), var(--inv-primary-light));
        border: none;
        color: #fff;
        border-radius: 10px;
        padding: 0.55rem 1.25rem;
        font-weight: 600;
    }

    .inv-btn-filter:hover { color: #fff; opacity: 0.92; }

    /* ── Table ── */
    .inv-table-wrap {
        border: 1px solid var(--inv-border);
        border-radius: var(--inv-radius);
        overflow: hidden;
        background: var(--inv-surface);
    }

    /* List/index tables — horizontal scroll so action buttons stay reachable */
    .inv-list-table-wrap {
        overflow: visible;
    }

    .inv-list-table-wrap .inv-hscroll-top {
        overflow-x: auto;
        overflow-y: hidden;
        height: 14px;
        margin-bottom: 2px;
        scrollbar-width: thin;
        border-radius: var(--inv-radius) var(--inv-radius) 0 0;
        background: #f8fafc;
    }

    .inv-list-table-wrap .inv-hscroll-top::-webkit-scrollbar {
        height: 8px;
    }

    .inv-list-table-wrap .inv-hscroll-top::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 999px;
    }

    .inv-list-table-wrap .inv-hscroll-top-inner {
        height: 1px;
    }

    .inv-list-table-wrap .table-responsive {
        overflow-x: auto;
        overflow-y: visible;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        border-radius: 0 0 var(--inv-radius) var(--inv-radius);
    }

    .inv-list-table-wrap .table-responsive::-webkit-scrollbar {
        height: 8px;
    }

    .inv-list-table-wrap .table-responsive::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 999px;
    }

    .inv-list-table-wrap .inv-table {
        min-width: 980px;
    }

    .inv-list-table-wrap .inv-actions-cell {
        position: sticky;
        right: 0;
        z-index: 2;
        background: #fff;
        box-shadow: -8px 0 16px rgba(15, 23, 42, 0.06);
    }

    .inv-list-table-wrap .inv-table thead .inv-actions-cell {
        background: linear-gradient(180deg, #f1f5f9, #e8edf3);
    }

    .inv-list-table-wrap .inv-table tbody tr:hover .inv-actions-cell {
        background: #f0fdfa;
    }

    .inv-table {
        margin: 0;
        font-size: 0.875rem;
    }

    .inv-table thead th {
        background: linear-gradient(180deg, #f1f5f9, #e8edf3);
        color: #334155;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 14px 16px;
        border-bottom: 2px solid var(--inv-border);
        white-space: nowrap;
    }

    .inv-table tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
    }

    .inv-table tbody tr {
        transition: background 0.12s ease;
    }

    .inv-table tbody tr:hover {
        background: #f0fdfa;
    }

    .inv-table tbody tr:last-child td {
        border-bottom: none;
    }

    .inv-inv-no {
        display: inline-block;
        font-family: 'SF Mono', 'Consolas', monospace;
        font-size: 0.78rem;
        font-weight: 700;
        background: var(--inv-accent-soft);
        color: var(--inv-primary);
        padding: 3px 10px;
        border-radius: 6px;
        border: 1px solid rgba(6,182,212,0.2);
    }

    .inv-patient-name {
        font-weight: 700;
        color: #0f172a;
    }

    .inv-patient-id {
        font-size: 0.78rem;
        color: var(--inv-muted);
    }

    .inv-amount {
        font-weight: 700;
        font-variant-numeric: tabular-nums;
    }

    .inv-amount.due { color: var(--inv-danger); }
    .inv-amount.paid { color: var(--inv-success); }

    .inv-progress-wrap {
        min-width: 80px;
    }

    .inv-progress-bar {
        height: 6px;
        border-radius: 999px;
        background: #e2e8f0;
        overflow: hidden;
        margin-top: 4px;
    }

    .inv-progress-fill {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, var(--inv-success), #34d399);
        transition: width 0.3s ease;
    }

    .inv-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .inv-status.complete { background: #ecfdf5; color: #047857; }
    .inv-status.pending { background: #fffbeb; color: #b45309; }
    .inv-status.delivery { background: #eff6ff; color: #1d4ed8; }

    .inv-status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
    }

    .inv-actions-cell {
        width: 200px;
        min-width: 200px;
        vertical-align: middle !important;
        text-align: right;
        padding-right: 14px !important;
    }

    .inv-actions-head {
        display: grid;
        grid-template-columns: repeat(5, 34px);
        gap: 6px;
        justify-content: end;
        margin-left: auto;
        color: #94a3b8;
        font-size: 0.72rem;
    }

    .inv-actions-head span {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 18px;
    }

    .inv-actions-grid {
        display: grid;
        grid-template-columns: repeat(5, 34px);
        gap: 6px;
        justify-content: end;
        align-items: center;
        margin-left: auto;
    }

    .inv-act-slot {
        width: 34px;
        height: 34px;
        display: block;
        flex-shrink: 0;
    }

    .inv-actions {
        display: contents;
    }

    .inv-act {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        color: #fff;
        text-decoration: none;
        font-size: 0.82rem;
        padding: 0;
        line-height: 1;
        flex-shrink: 0;
        transition: transform 0.12s ease, opacity 0.12s ease, box-shadow 0.12s ease;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.12);
    }

    .inv-act:hover { color: #fff; transform: scale(1.08); opacity: 0.9; }
    .inv-act.edit { background: #0ea5e9; }
    .inv-act.pay { background: #f59e0b; }
    .inv-act.pdf { background: #6366f1; }
    .inv-act.view { background: #10b981; }
    .inv-act.del { background: #ef4444; }

    .inv-empty {
        text-align: center;
        padding: 48px 20px;
        color: var(--inv-muted);
    }

    .inv-empty i {
        font-size: 2.5rem;
        opacity: 0.3;
        margin-bottom: 12px;
        display: block;
    }

    /* ── Create / Edit Layout ── */
    .inv-form-layout {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 20px;
        align-items: start;
    }

    @media (max-width: 1199px) {
        .inv-form-layout { grid-template-columns: 1fr; }
    }

    .inv-form-main { min-width: 0; }

    .inv-summary-sticky {
        position: sticky;
        top: 80px;
    }

    .inv-summary-card {
        background: #ffffff;
        border: 1px solid #d1fae5;
        border-radius: var(--inv-radius);
        padding: 0;
        color: #1e293b;
        box-shadow: 0 8px 32px rgba(5, 150, 105, 0.1);
        overflow: hidden;
    }

    .inv-summary-card h5 {
        margin: 0;
        padding: 16px 20px;
        font-weight: 700;
        font-size: 0.95rem;
        background: linear-gradient(135deg, #059669 0%, #10b981 50%, #34d399 100%);
        color: #fff;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .inv-summary-card h5 i {
        opacity: 0.9;
    }

    .inv-summary-body {
        padding: 16px 20px 20px;
    }

    .inv-summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
        color: #64748b;
    }

    .inv-summary-row:last-of-type { border-bottom: none; }

    .inv-summary-row.total {
        padding: 14px 12px;
        margin-top: 10px;
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        border: 1px solid #a7f3d0;
        border-radius: 10px;
        font-size: 1.05rem;
        font-weight: 800;
        color: #065f46;
    }

    .inv-summary-row .val {
        font-weight: 700;
        font-variant-numeric: tabular-nums;
        color: #0f172a;
    }

    .inv-summary-row.total .val {
        color: #047857;
        font-size: 1.15rem;
    }

    .inv-summary-row.due .val { color: #dc2626; }
    .inv-summary-row.paid .val { color: #059669; }

    .inv-summary-submit {
        width: calc(100% - 40px);
        margin: 0 20px 20px;
        background: linear-gradient(135deg, #059669, #10b981);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 0.75rem;
        font-weight: 800;
        font-size: 1rem;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        box-shadow: 0 6px 20px rgba(5, 150, 105, 0.3);
    }

    .inv-summary-submit:hover {
        transform: translateY(-2px);
        color: #fff;
        box-shadow: 0 10px 28px rgba(5, 150, 105, 0.4);
    }

    .inv-steps {
        display: flex;
        gap: 0;
        margin-bottom: 22px;
        background: var(--inv-surface);
        border: 1px solid var(--inv-border);
        border-radius: 12px;
        overflow: hidden;
    }

    .inv-step {
        flex: 1;
        text-align: center;
        padding: 12px 8px;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--inv-muted);
        border-right: 1px solid var(--inv-border);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .inv-step:last-child { border-right: none; }
    .inv-step.active { background: var(--inv-accent-soft); color: var(--inv-primary); }
    .inv-step-num {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 800;
    }

    .inv-step.active .inv-step-num {
        background: var(--inv-primary);
        color: #fff;
    }

    .inv-section {
        background: var(--inv-surface);
        border: 1px solid var(--inv-border);
        border-radius: var(--inv-radius);
        margin-bottom: 18px;
        overflow: hidden;
    }

    .inv-section-head {
        padding: 14px 20px;
        background: #f8fafc;
        border-bottom: 1px solid var(--inv-border);
        font-weight: 700;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.95rem;
    }

    .inv-section-head i { color: var(--inv-accent); }

    .inv-section-body { padding: 20px; }

    .inv-section-body .form-label,
    .inv-section-body label {
        font-size: 0.84rem;
        font-weight: 600;
        color: #475569;
    }

    .inv-section-body .form-control,
    .inv-section-body .form-select {
        border-radius: 10px;
        border-color: #cbd5e1;
    }

    .inv-test-search {
        position: relative;
    }

    .inv-test-search i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--inv-muted);
    }

    .inv-test-search input {
        padding-left: 40px !important;
    }

    .inv-add-test-btn {
        background: linear-gradient(135deg, var(--inv-primary), var(--inv-primary-light));
        border: none;
        color: #fff;
        border-radius: 10px;
        padding: 0.55rem 1.2rem;
        font-weight: 600;
    }

    .inv-add-test-btn:hover { color: #fff; opacity: 0.92; }

    #ordered-products.inv-table thead th {
        background: #f1f5f9;
        font-size: 0.78rem;
    }

    /* ── Show / Detail ── */
    .inv-detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 14px;
    }

    .inv-detail-item {
        background: #f8fafc;
        border: 1px solid var(--inv-border);
        border-radius: 12px;
        padding: 16px;
    }

    .inv-detail-item label {
        display: block;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--inv-muted);
        margin-bottom: 4px;
    }

    .inv-detail-item span {
        font-weight: 700;
        color: #0f172a;
        font-size: 1rem;
    }

    .inv-toggle-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 22px;
        background: linear-gradient(135deg, #f0fdfa, #ecfeff);
        border: 1px solid rgba(6,182,212,0.2);
        border-radius: 14px;
        margin-bottom: 20px;
    }

    .inv-modal .modal-content {
        border: none;
        border-radius: var(--inv-radius);
        overflow: hidden;
    }

    .inv-modal .modal-header {
        background: linear-gradient(135deg, var(--inv-primary), var(--inv-primary-light));
        color: #fff;
        border: none;
    }

    .inv-modal .modal-header .btn-close { filter: brightness(0) invert(1); }

    .select2-container--default .select2-selection--single {
        border-radius: 10px !important;
        border-color: #cbd5e1 !important;
        min-height: 40px !important;
        padding: 4px 8px !important;
    }

    @media (max-width: 768px) {
        .inv-hero { padding: 20px; }
        .inv-hero-title { font-size: 1.3rem; }
        .inv-kpi-grid { grid-template-columns: 1fr 1fr; }

        /* List/index tables only — not invoice create/edit form table */
        .inv-table-wrap:not(.inv-form-table-wrap) .inv-table thead { display: none; }
        .inv-table-wrap:not(.inv-form-table-wrap) .inv-table tbody tr {
            display: block;
            padding: 14px;
            border-bottom: 8px solid #f1f5f9;
        }
        .inv-table-wrap:not(.inv-form-table-wrap) .inv-table tbody td {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border: none;
        }
        .inv-table-wrap:not(.inv-form-table-wrap) .inv-table tbody td::before {
            content: attr(data-label);
            font-weight: 700;
            color: var(--inv-muted);
            font-size: 0.78rem;
        }

        /* Wide list tables keep normal table + horizontal scroll */
        .inv-list-table-wrap .inv-table thead {
            display: table-header-group !important;
        }

        .inv-list-table-wrap .inv-table tbody tr {
            display: table-row !important;
            padding: 0;
            border-bottom: none;
        }

        .inv-list-table-wrap .inv-table tbody td {
            display: table-cell !important;
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .inv-list-table-wrap .inv-table tbody td::before {
            display: none !important;
        }

        .inv-list-table-wrap .inv-actions-cell {
            min-width: 200px;
        }

        .inv-list-table-wrap .inv-actions-grid {
            justify-content: flex-end;
        }
    }

    /* ── Invoice create / edit — mobile ── */
    @media (max-width: 991.98px) {
        .inv-page.container-fluid {
            padding-left: 0.65rem;
            padding-right: 0.65rem;
            padding-bottom: 7.5rem;
        }

        .inv-hero {
            padding: 18px 16px;
            margin-bottom: 14px;
        }

        .inv-hero-inner {
            flex-direction: column;
            align-items: stretch;
        }

        .inv-hero-left {
            gap: 12px;
        }

        .inv-hero-icon {
            width: 48px;
            height: 48px;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .inv-hero-title {
            font-size: 1.25rem;
        }

        .inv-hero-sub {
            font-size: 0.84rem;
            word-break: break-word;
        }

        .inv-hero-actions {
            width: 100%;
        }

        .inv-btn-glass {
            width: 100%;
            justify-content: center;
        }

        .inv-steps {
            flex-direction: column;
            margin-bottom: 14px;
        }

        .inv-step {
            border-right: none;
            border-bottom: 1px solid var(--inv-border);
            justify-content: flex-start;
            padding: 10px 14px;
            font-size: 0.8rem;
        }

        .inv-step:last-child {
            border-bottom: none;
        }

        .inv-section {
            margin-bottom: 14px;
        }

        .inv-section-head {
            padding: 12px 14px;
            font-size: 0.88rem;
        }

        .inv-section-body {
            padding: 14px;
        }

        .inv-section-body .form-control,
        .inv-section-body .form-select {
            min-height: 44px;
            font-size: 16px;
        }

        .inv-add-test-btn {
            width: 100%;
            justify-content: center;
            padding: 0.65rem 1rem;
        }

        .inv-form-table-wrap {
            border: none;
            background: transparent;
        }

        .inv-form-table-wrap .table-responsive {
            overflow: visible;
        }

        /* Product rows as cards */
        .inv-form-table-wrap #ordered-products thead {
            display: none;
        }

        .inv-form-table-wrap #ordered-products tbody tr {
            display: block;
            margin-bottom: 10px;
            padding: 12px 14px;
            border: 1px solid var(--inv-border);
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        }

        .inv-form-table-wrap #ordered-products tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            padding: 5px 0;
            border: none;
            text-align: right;
        }

        .inv-form-table-wrap #ordered-products tbody td::before {
            content: attr(data-label);
            font-weight: 700;
            color: var(--inv-muted);
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            text-align: left;
            flex: 1;
        }

        .inv-form-table-wrap #ordered-products tbody td:last-child {
            justify-content: flex-end;
            padding-top: 8px;
            border-top: 1px dashed #e2e8f0;
            margin-top: 4px;
        }

        .inv-form-table-wrap #ordered-products tbody td:last-child::before {
            display: none;
        }

        /* Payment / discount block */
        .inv-form-table-wrap #ordered-products tfoot {
            display: block;
            margin-top: 12px;
        }

        .inv-form-table-wrap #ordered-products tfoot tr {
            display: block;
            padding: 12px 14px;
            margin-bottom: 8px;
            border: 1px solid var(--inv-border);
            border-radius: 12px;
            background: #f8fafc;
        }

        .inv-form-table-wrap #ordered-products tfoot td {
            display: block;
            width: 100% !important;
            padding: 4px 0;
            border: none;
        }

        .inv-form-table-wrap #ordered-products tfoot td:empty {
            display: none;
        }

        .inv-form-table-wrap #ordered-products tfoot strong {
            display: block;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: var(--inv-muted);
            margin-bottom: 6px;
        }

        .inv-form-table-wrap #ordered-products tfoot input.form-control {
            width: 100%;
            min-height: 44px;
            font-size: 16px;
        }

        .inv-form-table-wrap #ordered-products tfoot label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-right: 16px;
            margin-bottom: 4px;
            font-size: 0.9rem;
        }

        /* Fixed payment summary bar */
        .inv-summary-sticky {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            top: auto;
            z-index: 1025;
            padding: 0 8px calc(8px + env(safe-area-inset-bottom, 0px));
        }

        .inv-summary-card {
            border-radius: 16px 16px 12px 12px;
            box-shadow: 0 -10px 40px rgba(15, 23, 42, 0.16);
        }

        .inv-summary-card h5 {
            padding: 10px 16px;
            font-size: 0.88rem;
        }

        .inv-summary-body {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 2px 14px;
            padding: 8px 16px 10px;
        }

        .inv-summary-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 1px;
            padding: 6px 0;
            border-bottom: none;
            font-size: 0.78rem;
        }

        .inv-summary-row .val {
            font-size: 0.92rem;
        }

        .inv-summary-row.total {
            grid-column: 1 / -1;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            margin-top: 4px;
            padding: 10px 12px;
        }

        .inv-section-body label .btn {
            margin-top: 4px;
            margin-left: 0 !important;
        }

        .inv-section-body .form-group label {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px;
        }

        .ui-autocomplete {
            max-width: calc(100vw - 2rem);
            z-index: 2000 !important;
        }
    }

    @media (max-width: 575.98px) {
        .inv-kpi-grid { grid-template-columns: 1fr; }

        .inv-summary-body {
            grid-template-columns: 1fr 1fr;
        }

        .inv-section-body .col-md-2,
        .inv-section-body .col-md-4,
        .inv-section-body .col-md-8 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
</style>
