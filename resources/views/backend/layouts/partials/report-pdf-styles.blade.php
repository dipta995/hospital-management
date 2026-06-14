<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
        font-size: 11px;
        line-height: 1.45;
        color: #1e293b;
        background: #fff;
    }

    .rpdf-page {
        width: 100%;
        padding: 12px 14px 28px;
    }

    /* Header */
    .rpdf-header {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 14px;
        border-bottom: 3px solid #0369a1;
        padding-bottom: 10px;
    }

    .rpdf-header td { vertical-align: middle; padding: 4px 0; }

    .rpdf-logo-cell { width: 28%; }

    .rpdf-logo {
        max-height: 72px;
        max-width: 180px;
    }

    .rpdf-company-name {
        font-size: 18px;
        font-weight: bold;
        color: #0c4a6e;
        margin-bottom: 4px;
    }

    .rpdf-company-meta {
        font-size: 9px;
        color: #475569;
        line-height: 1.5;
    }

    /* Title block */
    .rpdf-title-block {
        text-align: center;
        margin: 12px 0 14px;
        padding: 10px 12px;
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 6px;
    }

    .rpdf-title {
        font-size: 16px;
        font-weight: bold;
        color: #0c4a6e;
        margin-bottom: 4px;
    }

    .rpdf-subtitle {
        font-size: 10px;
        color: #64748b;
    }

    .rpdf-period {
        display: inline-block;
        margin-top: 6px;
        padding: 3px 10px;
        background: #fff;
        border: 1px solid #cbd5e1;
        border-radius: 999px;
        font-size: 9px;
        font-weight: bold;
        color: #0369a1;
    }

    /* KPI summary row */
    .rpdf-kpi-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 6px;
        margin-bottom: 14px;
    }

    .rpdf-kpi-table td {
        width: 25%;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 8px 10px;
        text-align: center;
        vertical-align: top;
    }

    .rpdf-kpi-label {
        font-size: 8px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        font-weight: bold;
        margin-bottom: 4px;
    }

    .rpdf-kpi-value {
        font-size: 13px;
        font-weight: bold;
        color: #0f172a;
    }

    .rpdf-kpi-value.success { color: #047857; }
    .rpdf-kpi-value.danger { color: #b91c1c; }
    .rpdf-kpi-value.warning { color: #c2410c; }

    /* Data tables */
    .rpdf-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 6px;
        font-size: 10px;
    }

    .rpdf-table th,
    .rpdf-table td {
        border: 1px solid #cbd5e1;
        padding: 6px 8px;
        vertical-align: top;
    }

    .rpdf-table thead th {
        background: #0369a1;
        color: #fff;
        font-weight: bold;
        font-size: 9px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .rpdf-table tfoot th,
    .rpdf-table tfoot td {
        background: #f1f5f9;
        font-weight: bold;
    }

    .rpdf-row-date td {
        background: #ecfeff !important;
        color: #0c4a6e;
        font-weight: bold;
        border-top: 2px solid #7dd3fc;
    }

    .rpdf-row-group td {
        background: #fffbeb !important;
        color: #92400e;
        font-weight: bold;
    }

    .rpdf-row-subtotal td {
        background: #fef3c7 !important;
        font-weight: bold;
    }

    .rpdf-row-total td {
        background: #e0f2fe !important;
        font-weight: bold;
        font-size: 11px;
    }

    .text-end { text-align: right; }
    .text-center { text-align: center; }
    .text-muted { color: #64748b; }
    .fw-bold { font-weight: bold; }

    /* Status badges */
    .rpdf-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 8px;
        font-weight: bold;
    }

    .rpdf-badge-paid { background: #d1fae5; color: #047857; }
    .rpdf-badge-unpaid { background: #fee2e2; color: #b91c1c; }
    .rpdf-badge-extra { background: #dbeafe; color: #1d4ed8; }

    /* Balance helper output */
    .rpdf-balance-box {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 16px 18px;
        background: #f8fafc;
    }

    .rpdf-balance-box h5 {
        font-size: 13px;
        color: #0c4a6e;
        margin-bottom: 12px;
        font-weight: bold;
    }

    .rpdf-balance-box p {
        font-size: 11px;
        margin-bottom: 6px;
        padding-bottom: 6px;
        border-bottom: 1px dashed #e2e8f0;
    }

    .rpdf-balance-box hr {
        border: none;
        border-top: 1px solid #cbd5e1;
        margin: 8px 0;
    }

    /* Actions & footer */
    .rpdf-actions {
        text-align: right;
        margin-bottom: 10px;
    }

    .rpdf-btn {
        display: inline-block;
        padding: 6px 14px;
        background: #0369a1;
        color: #fff;
        border: none;
        border-radius: 4px;
        font-size: 11px;
        cursor: pointer;
        text-decoration: none;
    }

    .rpdf-footer {
        margin-top: 18px;
        padding-top: 8px;
        border-top: 1px solid #e2e8f0;
        font-size: 8px;
        color: #94a3b8;
        text-align: center;
    }

    @media print {
        .no-print { display: none !important; }
        body { margin: 0; }
        .rpdf-page { padding: 0; }
    }
</style>
