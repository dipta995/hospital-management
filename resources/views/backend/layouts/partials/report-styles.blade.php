@include('backend.layouts.partials.invoice-styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<style>
    /* Report filter toolbar */
    .inv-filter-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: flex-end;
        padding: 16px 18px;
        background: #f8fafc;
        border-bottom: 1px solid var(--inv-border);
    }

    .inv-filter-toolbar .form-label {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: var(--inv-muted);
        margin-bottom: 4px;
    }

    .inv-filter-toolbar .filter-field { min-width: 140px; flex: 1; }

    .inv-filter-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }

    /* Grouped collection tables */
    .report-date-row td {
        background: linear-gradient(90deg, #ecfeff, #f0f9ff) !important;
        font-weight: 800;
        color: #0c4a6e;
        border-top: 2px solid #bae6fd;
        padding: 12px 14px !important;
    }

    .report-group-row td {
        background: #fffbeb !important;
        font-weight: 700;
        color: #92400e;
    }

    .report-section-head th {
        background: #f1f5f9 !important;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #475569;
    }

    /* Reference / status badges */
    .rep-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
    }
    .rep-badge-paid { background: #ecfdf5; color: #047857; }
    .rep-badge-pending { background: #fef2f2; color: #b91c1c; }
    .rep-badge-extra { background: #eff6ff; color: #1d4ed8; }
    .rep-badge-due { background: #fff7ed; color: #c2410c; }

    /* Helper HTML output (balance reports) — do not change helper logic */
    .report-helper-output {
        padding: 8px 0;
    }
    .report-helper-output > div {
        font-family: inherit !important;
        max-width: 100% !important;
        border: 1px solid var(--inv-border) !important;
        border-radius: 14px !important;
        background: #fff !important;
        padding: 24px !important;
        box-shadow: 0 8px 24px rgba(12, 74, 110, 0.06);
    }
    .report-helper-output h5 {
        color: #0c4a6e;
        font-weight: 800;
        margin-bottom: 16px;
    }
    .report-helper-output p {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        padding: 6px 0;
        border-bottom: 1px dashed #e2e8f0;
    }
    .report-helper-output hr { opacity: 0.4; }
    .report-helper-output table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }
    .report-helper-output table th,
    .report-helper-output table td {
        padding: 10px 12px;
        border-bottom: 1px solid #e2e8f0;
    }
    .report-helper-output table thead th {
        background: #f8fafc;
        font-weight: 700;
        color: #475569;
    }

    /* Pay panel (reference payment) */
    .report-pay-panel {
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 12px;
        padding: 16px 18px;
        margin-bottom: 16px;
    }

    .report-export-card {
        border: 1px solid var(--inv-border);
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 16px;
        background: #fafafa;
    }
    .report-export-card h6 {
        font-weight: 800;
        color: #0c4a6e;
        margin-bottom: 12px;
    }

    .inv-page .select2-container--default .select2-selection--single {
        min-height: 42px;
        border-radius: 10px;
        border-color: #cbd5e1;
        display: flex;
        align-items: center;
        padding: 4px 10px;
    }
    .inv-page .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
        background-color: #0369a1;
    }

    .report-period-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 999px;
        background: #ecfeff;
        color: #0e7490;
        font-size: 0.85rem;
        font-weight: 700;
        margin-bottom: 16px;
    }
</style>
