@include('backend.layouts.partials.crud-styles')
<style>
    .lab-page {
        --lab-primary: #1e3a8a;
        --lab-accent: #2563eb;
        --lab-soft: #eff6ff;
        --lab-border: #e2e8f0;
    }

    .lab-page .crud-hero,
    .lab-theme .crud-hero {
        background: linear-gradient(135deg, #0f2147 0%, #1e3a8a 52%, #2563eb 100%);
        box-shadow: 0 12px 32px rgba(30, 58, 138, 0.18);
    }

    .lab-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }

    .lab-kpi {
        background: #fff;
        border: 1px solid var(--lab-border);
        border-radius: 14px;
        padding: 14px 16px;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.04);
    }

    .lab-kpi-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        margin-bottom: 4px;
    }

    .lab-kpi-value {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }

    .lab-kpi-sub {
        font-size: 0.74rem;
        color: #64748b;
        margin-top: 4px;
    }

    .lab-status-guide {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        margin-bottom: 14px;
    }

    .lab-status-guide .guide-label {
        font-size: 0.78rem;
        font-weight: 700;
        color: #64748b;
    }

    .lab-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.74rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .lab-badge.pending { background: #fee2e2; color: #b91c1c; }
    .lab-badge.processing { background: #fef3c7; color: #92400e; }
    .lab-badge.complete { background: #dbeafe; color: #1d4ed8; }
    .lab-badge.rejected { background: #f1f5f9; color: #475569; }
    .lab-badge.invoice-pending { background: #fef3c7; color: #92400e; }
    .lab-badge.invoice-complete { background: #dcfce7; color: #15803d; }

    .lab-test-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        max-width: 360px;
    }

    .lab-patient-cell strong {
        display: block;
        color: #0f172a;
        font-size: 0.92rem;
    }

    .lab-patient-meta {
        font-size: 0.78rem;
        color: #64748b;
        margin-top: 2px;
    }

    .lab-panel {
        background: #fff;
        border: 1px solid var(--lab-border);
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 18px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
    }

    .lab-panel-head {
        background: linear-gradient(180deg, var(--lab-soft), #fff);
        border-bottom: 1px solid #dbeafe;
        padding: 12px 18px;
        font-weight: 800;
        color: var(--lab-primary);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        font-size: 0.92rem;
    }

    .lab-panel-body { padding: 0; }

    .lab-tests-table { margin-bottom: 0; }
    .lab-tests-table thead th {
        background: #f8fafc;
        font-size: 0.76rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        white-space: nowrap;
        border-bottom: 2px solid #dbeafe;
        font-weight: 800;
        color: #475569;
    }

    .lab-tests-table tbody td {
        vertical-align: top;
        padding: 14px 12px;
        border-color: #f1f5f9;
    }

    .lab-test-name { font-weight: 800; color: #0f172a; margin-bottom: 2px; }
    .lab-test-price { color: #64748b; font-size: 0.86rem; }

    .lab-action-links { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; }
    .lab-action-links .btn { font-size: 0.76rem; padding: 4px 10px; }

    .lab-reagent-box {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        padding: 12px;
        margin-top: 4px;
    }

    .lab-reagent-box .form-label {
        font-size: 0.76rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: 4px;
    }

    .lab-reagent-tags {
        margin-top: 8px;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
    }

    .lab-note-preview {
        font-size: 0.8rem;
        color: #64748b;
        margin-top: 6px;
        max-width: 240px;
    }

    .lab-summary-pills { display: flex; flex-wrap: wrap; gap: 8px; }
    .lab-summary-pill {
        font-size: 0.74rem;
        padding: 4px 10px;
        border-radius: 999px;
        background: var(--lab-soft);
        border: 1px solid #dbeafe;
        color: var(--lab-primary);
        font-weight: 700;
    }

    .lab-page .select2-container { width: 100% !important; min-width: 200px; }
    .lab-empty { text-align: center; padding: 32px 16px; color: #94a3b8; }

    .lab-crud-actions {
        display: inline-flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .lab-btn {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: #fff;
        font-size: 0.85rem;
        border: none;
        transition: transform 0.12s ease;
    }

    .lab-btn:hover { color: #fff; transform: translateY(-1px); }
    .lab-btn.view { background: #2563eb; }
    .lab-btn.pdf { background: #dc2626; }
    .lab-btn.edit { background: #0ea5e9; }
    .lab-btn.download { background: #059669; }

    @media (max-width: 768px) {
        .lab-tests-table thead { display: none; }
        .lab-tests-table tbody tr {
            display: block;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 0;
        }
        .lab-tests-table tbody td {
            display: block;
            border: none;
            padding: 6px 14px;
        }
        .lab-tests-table tbody td::before {
            content: attr(data-label);
            display: block;
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 4px;
        }
    }
</style>
